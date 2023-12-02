import cv2
from flask import Flask, request, jsonify, json
import matplotlib.pyplot as plt
import numpy as np
from sklearn.cluster import KMeans

app = Flask(__name__)

# Define the directory where uploaded images will be stored
sharedFolder = "./storage/app/public/images/"


# Route to upload an image and get Histogram in json maybe!
@app.route('/getColorHistogram', methods=['GET'])
def getColorHistogram():

    imageName = request.args.get('imageName')

    # Lisez l'image depuis le fichier
    image = cv2.imread(sharedFolder + imageName)

    if image is None:
        print("Failed to load the image.")

    # Calculez l'histogramme RVB de l'image
    histB = cv2.calcHist([image], [0], None, [256], [0, 256])
    histG = cv2.calcHist([image], [1], None, [256], [0, 256])
    histR = cv2.calcHist([image], [2], None, [256], [0, 256])

    # Flatten the histogram into a one-dimensional array
    # hist = hist.flatten()


  
        
    # Create a histogram plot
    plt.figure(figsize=(10, 6))
    plt.title('Histogram')
    plt.plot(histR, color='red', label='R')
    plt.plot(histG, color='green', label='G')
    plt.plot(histB, color='blue', label='B')
    path = sharedFolder + "Histogram_" + imageName

    # Save the histogram as an image
    plt.savefig(path)

    # Close the plot
    plt.close()

    return "Histogram_" + imageName


@app.route('/crop', methods=['GET'])
def crop():

    imageName = request.args.get('imageName')
    x = int(request.args.get('x'))
    y = int(request.args.get('y'))
    width = int(request.args.get('width'))
    height = int(request.args.get('height'))

    image = cv2.imread(sharedFolder + imageName)

    # Crop the region from the original image
    cropped_region = image[y:y + height, x:x + width]

    path = sharedFolder + "Crop" + str(height) + str(width) + imageName

    # Save the cropped region as a new image in a specific directory
    cv2.imwrite(path, cropped_region)

    return "Crop" + str(height) + str(width) + imageName


@app.route('/clusteringByColor', methods=['GET'])
def clusteringByColor():

    # Charger l'image
    imageName = request.args.get('imageName')

    k = int(request.args.get('k'))  # Le nombre de clusters (couleurs dominantes) à trouver

    image = cv2.imread(sharedFolder + imageName)

    # Redimensionner l'image pour le clustering
    pixels = image.reshape((-1, 3))

    kmeans = KMeans(n_clusters=k)
    kmeans.fit(pixels)

    # Obtenir les couleurs dominantes
    dominant_colors = kmeans.cluster_centers_.astype(int)

    # Créer un histogramme des couleurs dominantes
    hist, bins = np.histogram(kmeans.labels_, bins=range(k + 1))
    hist = hist / hist.sum()  # Normaliser l'histogramme

    # Create a bar plot for the histogram
    plt.bar(range(k), hist, color=[(c[2] / 255, c[1] / 255, c[0] / 255) for c in dominant_colors])
    plt.title('Histogram of Dominant Colors')
    plt.xlabel('Cluster (Dominant Color)')
    plt.ylabel('Frequency')

    path = sharedFolder + "ClusteringColorHistogram_" + str(k) + imageName 

    # Sauvegarder l'image affichée dans l'histogramme
    plt.savefig(path)

    # Close the plot
    plt.close()

    return "ClusteringColorHistogram_" + str(k) + imageName 


@app.route('/getColorMoment', methods=['GET'])
def getColorMoment():

    # Charger l'image
    imageName = request.args.get('imageName')

    image = cv2.imread(sharedFolder + imageName)


    # Convertir l'image en espace de couleur Lab
    lab_image = cv2.cvtColor(image, cv2.COLOR_BGR2Lab)

    # Calculer les moments de couleur
    mean_lab = cv2.mean(lab_image)

    # Extraire les valeurs de moments de couleur
    mean_l, mean_a, mean_b = mean_lab[:3]

    # Étiquettes des canaux
    channel_labels = ['B', 'G', 'R']

    # Valeurs des canaux
    channel_values = [mean_l, mean_a, mean_b]

    # Créer un graphique en barres
    plt.bar(channel_labels, channel_values, color=['blue', 'green', 'red'])
    plt.title('Moments de Couleur')
    plt.ylabel('Valeur moyenne')

    path = sharedFolder + "ColorsMomentChart_" + imageName

    plt.savefig(path)

    # Close the plot
    plt.close()

    return "ColorsMomentChart_" + imageName


if __name__ == '__main__':
    app.run()

