import json
import os

import numpy as np
from flask import Flask, request
import ProcessObject
from ProcessAllObjects import ProcessDirectory

app = Flask(__name__)

JsonFileName = "all_results.json"
sharedFolder = "./sharedFolder/"

json_results = {}  # to store features of all the OBJ file in out models example dir

isFirstTime = True


# Function to run before the first request
@app.before_request
def before_first_request():
    global isFirstTime
    if isFirstTime:
        global json_results
        json_results = ProcessDirectory.read_results_from_json(JsonFileName)
        print("data imported successfully !")
        isFirstTime = False


def euclidean_distance(features1, features2):
    euclidean_dist = np.linalg.norm(features1 - features2)
    return euclidean_dist


@app.route("/", methods=["GET"])
def hello_world():  # put application's code here
    return 'Hello World!'


@app.route("/getSimilarObjs", methods=["GET"])
def getSimilarObjs():
    objName = request.args.get("name")

    newObject = ProcessObject.MeshFeaturesCalculator.calculate_features(
        sharedFolder + objName)  # objName like: "test.obj"
    newObjectData = [
        np.array(newObject.get('inertia_matrix')),
        np.array(newObject.get('moment_along_principal_axes')),
        np.array(newObject.get('averages_along_axes')),
        np.array(newObject.get('variances_along_axes')),
    ]

    distances = {}

    # Iterate through the outer dictionary
    for outer_key, inner_dict in json_results.items():
        # Iterate through the second-level dictionary
        for inner_key, features_dict in inner_dict.items():
            d1 = euclidean_distance(
                np.array(features_dict.get('inertia_matrix')),
                newObjectData[0]
            )

            d2 = euclidean_distance(
                np.array(features_dict.get('moment_along_principal_axes')),
                newObjectData[1]
            )

            d3 = euclidean_distance(
                np.array(features_dict.get('averages_along_axes')),
                newObjectData[2]
            )

            d4 = euclidean_distance(
                np.array(features_dict.get('variances_along_axes')),
                newObjectData[3]
            )
            filename = inner_key
            filename = change_extension(filename, ".jpg")

            distances["3DPotteryDataset_v_1/3D Models/" + outer_key + "/" + filename] = d1 + d2 + d3 + d4

    sorted_dict_by_values = dict(sorted(distances.items(), key=lambda item: item[1]))
    return json.JSONEncoder().encode(list(sorted_dict_by_values.keys()))


def change_extension(filename, new_extension):
    # Split the filename and extension
    name, _ = os.path.splitext(filename)

    # Concatenate the new extension
    new_filename = name + new_extension

    return new_filename


if __name__ == "__main__":
    app.run()
