import os
import cv2
import numpy as np
import time
import mahotas as mh
from flask import Flask, request, jsonify
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler

app = Flask(__name__)

# dir where images are stored and accessed by name.
sharedFolder = "images/"


@app.route('/getRGBHistogram', methods=['GET'])
def getRGBHistogram():

    imageName = request.args.get('imageName')

    # Check if imageName is provided
    if imageName is None:
        return jsonify({'error': 'Image name not provided'})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({'error': 'Failed to load the image from the storage'})

    # Calculate RGB histograms
    histB = cv2.calcHist([image], [0], None, [256], [0, 256])
    histG = cv2.calcHist([image], [1], None, [256], [0, 256])
    histR = cv2.calcHist([image], [2], None, [256], [0, 256])

    # Prepare response data
    response_data = {
        'dataR': histR.tolist(),
        'dataG': histG.tolist(),
        'dataB': histB.tolist()
    }

    return jsonify(response_data), 200, {'Content-Type': 'application/json'}


@app.route('/getColorMoments', methods=['GET'])
def getColorMoments():

    imageName = request.args.get('imageName')

    # Check if imageName is provided
    if imageName is None:
        return jsonify({'error': 'Image name not provided'})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({'error': 'Failed to load the image from the storage'})

    # Convertir l'image en espace de couleur Lab
    lab_image = cv2.cvtColor(image, cv2.COLOR_BGR2Lab)

    # Calculer les moments de couleur
    mean_lab = cv2.mean(lab_image)

    # Extraire les valeurs de moments de couleur
    mean_l, mean_a, mean_b = mean_lab[:3]

    # Créer un dictionnaire avec les moments de couleur
    color_moments = {
        'mean_l': mean_l,
        'mean_a': mean_a,
        'mean_b': mean_b
    }

    # Retourner les données en format JSON
    return jsonify(color_moments), 200, {'Content-Type': 'application/json'}


@app.route('/getClusteringByRGBcolors', methods=['GET'])
def getClusteringByRGBcolors():

    imageName = request.args.get('imageName')

    # Check if imageName is provided
    if imageName is None:
        return jsonify({'error': 'Image name not provided'})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({'error': 'Failed to load the image from the storage'})

    k = int(request.args.get('k'))

    # Perform clustering
    examples = image.reshape((image.shape[0] * image.shape[1], 3))
    kmeans = KMeans(n_clusters=k)
    kmeans.fit(examples)

    # Get the dominant colors
    dominant_colors = kmeans.cluster_centers_.astype(int)

    # Convert the RGB values to hexadecimal for better visualization
    hex_colors = ['#%02x%02x%02x' % (r, g, b) for r, g, b in dominant_colors]

    # Count occurrences of each color
    color_counts = np.histogram(kmeans.labels_, bins=range(k + 1))[0]

    # Convert the histogram data to JSON
    histogram_data = {'color_counts': {hex_color: int(count) for hex_color, count in zip(hex_colors, color_counts)}}

    # Return the histogram data as JSON
    return jsonify(histogram_data), 200, {'Content-Type': 'application/json'}


@app.route('/getTraumaCharacteristics', methods=['GET'])
def getTraumaCharacteristics():

    imageName = request.args.get('imageName')

    # Check if imageName is provided
    if imageName is None:
        return jsonify({'error': 'Image name not provided'})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({'error': 'Failed to load the image from the storage'})

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
        'contrast': contrast,
        'directionality': directionality,
        'coarseness': coarseness,
        'linelikeness': linelikeness,
        'regularity': regularity,
        'roughness': roughness
    }

    return jsonify(response_data)


@app.route('/getGaborData', methods=['GET'])
def getGaborData():
    imageName = request.args.get('imageName')

    # Check if imageName is provided
    if imageName is None:
        return jsonify({'error': 'Image name not provided'})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({'error': 'Failed to load the image from the storage'})

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
                ktype=cv2.CV_32F  # datatype of filter coefficients
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

        statistics.append({
            'mean': mean_value,
            'std': std_value
        })

    means = [stat['mean'] for stat in statistics]
    stds = [stat['std'] for stat in statistics]

    # Reshape to a 2D array for StandardScaler
    means = np.array(means).reshape(-1, 1)
    stds = np.array(stds).reshape(-1, 1)

    # Use StandardScaler for normalization
    scaler = StandardScaler()
    normalized_means = scaler.fit_transform(means)
    normalized_stds = scaler.fit_transform(stds)

    # Update the statistics with normalized values
    for i, stat in enumerate(statistics):
        stat['mean'] = normalized_means[i][0]
        stat['std'] = normalized_stds[i][0]

    # Return the statistics as clean JSON using Flask's jsonify
    return jsonify(statistics)


@app.route('/cropImage', methods=['GET'])
def cropImage():

    imageName = request.args.get('imageName')

    # Check if imageName is provided
    if imageName is None:
        return jsonify({'error': 'Image name not provided'})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({'error': 'Failed to load the image from the storage'})

    x = int(request.args.get('x'))
    y = int(request.args.get('y'))
    width = int(request.args.get('width'))
    height = int(request.args.get('height'))

    # Crop the image based on the provided parameters
    cropped_image = image[y:y + height, x:x + width]

    if cropped_image is not None:

        cropped_image_path = os.path.join(sharedFolder, 'cropped_' + str(time.time()) + imageName)
        cv2.imwrite(cropped_image_path, cropped_image)

        return jsonify({'success': 'Image cropped and saved successfully', 'cropped_image_path': cropped_image_path})
    else:
        return jsonify({'error': 'Failed to crop the image'})


@app.route('/resizeImage', methods=['GET'])
def resizeImage():

    imageName = request.args.get('imageName')
    scale_factor = float(request.args.get('scaleFactor'))

    # Check if imageName is provided
    if imageName is None:
        return jsonify({'error': 'Image name not provided'})

    # Load the image
    image = cv2.imread(sharedFolder + imageName)

    # Check if the image is loaded successfully
    if image is None:
        print("Failed to load the image from the storage.")
        return jsonify({'error': 'Failed to load the image from the storage'})

    # Resize the image
    resized_image = cv2.resize(image, None, fx=scale_factor, fy=scale_factor)

    if resized_image is not None:
        # Save or process the resized image as needed

        # For example, you can save the resized image
        resized_image_path = os.path.join(sharedFolder, 'resized_' + str(time.time()) + imageName)
        cv2.imwrite(resized_image_path, resized_image)

        return jsonify({'success': 'Image resized and saved successfully', 'resized_image_path': resized_image_path})
    else:
        return jsonify({'error': 'Failed to resize the image'})


if __name__ == '__main__':
    app.run()
