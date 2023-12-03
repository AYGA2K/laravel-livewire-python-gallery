import json
import os
import cv2
import numpy as np
import time
import mahotas as mh
from flask import Flask, request, jsonify
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
import mysql.connector

app = Flask(__name__)

# dir where images are stored and accessed by name.
sharedFolder = "./public/storage/"

# Connect to the MySQL database
connection = mysql.connector.connect(
    host='localhost',
    user='makarov',
    password='19841984',
    database='laravel'
)


@app.route("/preprocessing", methods=["GET"])
def preprocessing():

    imageName = request.args.get("imageName")

    # Check if imageName is provided
    if imageName is None:
        return jsonify({"error": "Image name not provided"})

    (histR, histG, histB) = getRGBHistogram(imageName)
    (histR, histG, histB) = (json.dumps(histR), json.dumps(histG), json.dumps(histB))
    ColorsMoment = json.dumps(getColorsMoment(imageName))
    trauma = json.dumps(getTraumaCharacteristics(imageName))
    gabor = json.dumps(getGaborData(imageName))

    # Update data in the table
    update_data_query = "UPDATE images SET HistoR = %s, HistoG = %s, HistoB = %s, ColorM = %s, Trauma = %s, Gabor = %s WHERE name = %s"

    # Create a cursor to interact with the database
    cursor = connection.cursor()

    cursor.execute(update_data_query, (histR, histG, histB , ColorsMoment, trauma, gabor, imageName))

    #Commit the changes
    connection.commit()

    # Close the cursor and connection
    cursor.close()
    # connection.close()

    return "success"


@app.route("/getSimilarImages", methods=["GET"])
def getSimilarImages():

    imageName = request.args.get("imageName")

    # Check if imageName is provided
    if imageName is None:
        return jsonify({"error": "Image name not provided"})

    # Create a cursor to interact with the database
    cursor = connection.cursor()

    # Execute a SELECT query
    select_query = "SELECT * FROM images"
    cursor.execute(select_query)

    # Fetch all rows from the result set
    result = cursor.fetchall()


    final_array = []
    for image in result:
        # Call each function to get the relevant JSON strings
        histR = eval(json.loads(image[4]))
        histR_sum = np.sum(histR)
        histR_normal = np.divide(histR, histR_sum)
        histG = eval(json.loads(image[5]))
        histG_sum = np.sum(histG)
        histG_normal = np.divide(histG, histG_sum)
        histB = eval(json.loads(image[6]))
        histB_sum = np.sum(histB)
        histB_normal = np.divide(histB, histB_sum)
        colorM = eval(json.loads(image[7]))
        mean_l = colorM["mean_l"]
        mean_l_sum = np.sum(mean_l)
        mean_l_normal = np.divide(mean_l, mean_l_sum)
        mean_a = colorM["mean_a"]
        mean_a_sum = np.sum(mean_a)
        mean_a_normal = np.divide(mean_a, mean_a_sum)
        mean_b = colorM["mean_b"]
        mean_b_sum = np.sum(mean_b)
        mean_b_normal = np.divide(mean_b, mean_b_sum)
        
        trauma = eval(json.loads(image[8]))
        contrast = trauma["contrast"]
        contrast_sum = np.sum(contrast)
        contrast_normal = np.divide(contrast, contrast_sum)

        
        roughness = trauma["roughness"]
        roughness_sum = np.sum(roughness)
        roughness_normal = np.divide(contrast, contrast_sum)

        linelikeness = trauma["linelikeness"]
        linelikeness_sum = np.sum(linelikeness)
        linelikeness_normal = np.divide(linelikeness, linelikeness_sum)

        regularity = trauma["regularity"]
        regularity_sum = np.sum(regularity)
        regularity_normal = np.divide(regularity, regularity_sum)

        coarseness = trauma["coarseness"]
        coarseness_sum = np.sum(coarseness)
        coarseness_normal = np.divide(coarseness, coarseness_sum)

        directionality = trauma["directionality"]
        directionality_sum = np.sum(directionality)
        directionality_normal = np.divide(directionality, directionality_sum)
        
        gabor = eval(json.loads(image[9]))
        print(gabor)
        gabor_means = gabor["mean"]
        gabor_std = gabor["std"]
        gabor_data = np.add(gabor_means, gabor_std)
        final_array.extend( np.add(np.add(np.add(np.add(np.add(np.add(np.add(np.add(np.add(np.add(np.add(np.add(histR_normal,histG_normal), histB_normal), mean_l_normal), mean_a_normal), mean_b_normal), contrast_normal), roughness_normal), linelikeness_normal), coarseness_normal), regularity_normal), directionality_normal ), gabor_data))

    # print(len(final_array))

    
    # Close the cursor and connection
    cursor.close()
    # connection.close()

    return "success"


def getRGBHistogram(name):
    # Load the image
    image = cv2.imread(sharedFolder + name)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({"error": "Failed to load the image from the storage"})

    # Calculate RGB histograms
    histB = cv2.calcHist([image], [0], None, [256], [0, 256])
    histG = cv2.calcHist([image], [1], None, [256], [0, 256])
    histR = cv2.calcHist([image], [2], None, [256], [0, 256])

    # Prepare response data
    histR, histG, histB = histR.flatten().tolist(), histG.flatten().tolist(), histB.flatten().tolist()

    return (json.JSONEncoder().encode(histR), json.JSONEncoder().encode(histG), json.JSONEncoder().encode(histB))


def getColorsMoment(name):
    # Load the image
    image = cv2.imread(sharedFolder + name)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({"error": "Failed to load the image from the storage"})

    # Convertir l'image en espace de couleur Lab
    lab_image = cv2.cvtColor(image, cv2.COLOR_BGR2Lab)

    # Calculer les moments de couleur
    mean_lab = cv2.mean(lab_image)

    # Extraire les valeurs de moments de couleur
    mean_l, mean_a, mean_b = mean_lab[:3]

    # Créer un dictionnaire avec les moments de couleur
    color_moments = {"mean_l": mean_l, "mean_a": mean_a, "mean_b": mean_b}

    # Retourner les données en format JSON
    return json.JSONEncoder().encode(color_moments)


@app.route("/getClusteringByRGBcolors", methods=["GET"])
def getClusteringByRGBcolors():

    imageName = request.args.get("imageName")

    # Check if imageName is provided
    if imageName is None:
        return jsonify({"error": "Image name not provided"})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({"error": "Failed to load the image from the storage"})

    k = int(request.args.get("k"))

    # Perform clustering
    examples = image.reshape((image.shape[0] * image.shape[1], 3))
    kmeans = KMeans(n_clusters=k)
    kmeans.fit(examples)

    # Get the dominant colors
    dominant_colors = kmeans.cluster_centers_.astype(int)

    # Convert the RGB values to hexadecimal for better visualization
    hex_colors = ["#%02x%02x%02x" % (r, g, b) for r, g, b in dominant_colors]

    # Count occurrences of each color
    color_counts = np.histogram(kmeans.labels_, bins=range(k + 1))[0]

    # Convert the histogram data to JSON
    histogram_data = {
        "color_counts": {
            hex_color: int(count) for hex_color, count in zip(hex_colors, color_counts)
        }
    }

    return json.JSONEncoder().encode(histogram_data)


def getTraumaCharacteristics(name):
    # Load the image
    image = cv2.imread(sharedFolder + name)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({"error": "Failed to load the image from the storage"})

    # Convert the image to grayscale
    gray_image = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

    # Calculate texture features using GLCM
    textures = mh.features.haralick(gray_image)

    # Extract specific texture features
    contrast = textures.mean(axis=0)[1]
    directionality = textures.mean(axis=0)[2]
    coarseness = textures.mean(axis=0)[4]
    linelikeness = textures.mean(axis=0)[6]
    regularity = textures.mean(axis=0)[8]
    roughness = textures.mean(axis=0)[9]

    response_data = {
        "contrast": contrast,
        "directionality": directionality,
        "coarseness": coarseness,
        "linelikeness": linelikeness,
        "regularity": regularity,
        "roughness": roughness,
    }

    return json.JSONEncoder().encode(response_data)

def getGaborData(name):

    # Load the image
    image = cv2.imread(sharedFolder + name)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({"error": "Failed to load the image from the storage"})

    # Define orientations and scales
    orientations = [0, 45, 90, 135]  # in degrees
    scales = [3, 6, 9]

    filtered_images = []

    for orientation in orientations:
        for scale in scales:
            # Create Gabor kernel
            gabor_kernel = cv2.getGaborKernel(
                (scale, scale),  # kernel size
                5.0,  # standard deviation of the kernel
                np.radians(orientation),  # orientation of the Gabor kernel
                10.0,  # wavelength of the sinusoidal factor
                0.5,  # spatial aspect ratio
                0,  # phase offset
                ktype=cv2.CV_32F,  # datatype of filter coefficients
            )

            # Apply the Gabor filter to the image
            filtered_image = cv2.filter2D(image, cv2.CV_8UC3, gabor_kernel)
            filtered_images.append(filtered_image)

    statistics = []

    for filtered_image in filtered_images:
        # Convert to grayscale
        gray_image = cv2.cvtColor(filtered_image, cv2.COLOR_BGR2GRAY)

        # Calculate mean and standard deviation
        mean_value = np.mean(gray_image)
        std_value = np.std(gray_image)

        statistics.append({"mean": mean_value, "std": std_value})

    means = [stat["mean"] for stat in statistics]
    stds = [stat["std"] for stat in statistics]

    # Reshape to a 2D array for StandardScaler
    means = np.array(means).reshape(-1, 1)
    stds = np.array(stds).reshape(-1, 1)

    # Use StandardScaler for normalization
    scaler = StandardScaler()
    normalized_means = scaler.fit_transform(means)
    normalized_stds = scaler.fit_transform(stds)

    # Update the statistics with normalized values
    for i, stat in enumerate(statistics):
        stat["mean"] = normalized_means[i][0]
        stat["std"] = normalized_stds[i][0]

    # Return the statistics as clean JSON
    return json.JSONEncoder().encode({"mean": means.flatten().tolist(), "std": stds.flatten().tolist()})


@app.route("/cropImage", methods=["GET"])
def cropImage():

    imageName = request.args.get("imageName")

    # Check if imageName is provided
    if imageName is None:
        return jsonify({"error": "Image name not provided"})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({"error": "Failed to load the image from the storage"})

    x = int(request.args.get("x"))
    y = int(request.args.get("y"))
    width = int(request.args.get("width"))
    height = int(request.args.get("height"))

    # Crop the image based on the provided parameters
    cropped_image = image[y: y + height, x: x + width]

    if cropped_image is not None:
        cropped_image_path = os.path.join(
            sharedFolder, "cropped_" + str(time.time()) + imageName
        )
        cv2.imwrite(cropped_image_path, cropped_image)

        return jsonify(
            {
                "success": "Image cropped and saved successfully",
                "cropped_image_path": cropped_image_path,
            }
        )
    else:
        return jsonify({"error": "Failed to crop the image"})


@app.route("/resizeImage", methods=["GET"])
def resizeImage():
    imageName = request.args.get("imageName")
    scale_factor = float(request.args.get("scaleFactor"))

    # Check if imageName is provided
    if imageName is None:
        return jsonify({"error": "Image name not provided"})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({"error": "Failed to load the image from the storage"})

    # Resize the image
    resized_image = cv2.resize(image, None, fx=scale_factor, fy=scale_factor)

    if resized_image is not None:
        # Save or process the resized image as needed

        # For example, you can save the resized image
        resized_image_path = os.path.join(
            sharedFolder, "resized_" + str(time.time()) + imageName
        )
        cv2.imwrite(resized_image_path, resized_image)

        return jsonify(
            {
                "success": "Image resized and saved successfully",
                "resized_image_path": resized_image_path,
            }
        )
    else:
        return jsonify({"error": "Failed to resize the image"})


if __name__ == "__main__":
    app.run()
