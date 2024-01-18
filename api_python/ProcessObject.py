import numpy as np
from scipy.spatial.transform import Rotation


class MeshFeaturesCalculator:
    @staticmethod
    def calculate_features(file_path):

        vertices, faces = MeshFeaturesCalculator.read_obj(file_path)

        normalized_vertices = MeshFeaturesCalculator.normalize_orientation_and_size(vertices, faces)

        center_of_mass = np.mean(normalized_vertices, axis=0)

        inertia_matrix, principal_axes = MeshFeaturesCalculator.get_inertia_matrix_and_principal_axes(
            normalized_vertices, faces, center_of_mass)

        # Calculate moments along principal axes
        moments_along_principal_axes = MeshFeaturesCalculator.calculate_moments_along_principal_axes(
            inertia_matrix, principal_axes)

        # Calculate average distance and variance along principal axes
        averages_along_axes, variances_along_axes = (
            MeshFeaturesCalculator.calculate_avg_distance_and_var_along_principal_axes(
                normalized_vertices, faces, principal_axes, center_of_mass)
        )

        inertia_matrix = MeshFeaturesCalculator.normalize_to_unit_norm(inertia_matrix)

        # Combine all results into a dictionary
        all_results = {
            "inertia_matrix": inertia_matrix.tolist(),
            "moment_along_principal_axes": moments_along_principal_axes.tolist(),
            "averages_along_axes": averages_along_axes,
            "variances_along_axes": variances_along_axes
        }

        return all_results

    @staticmethod
    def normalize_to_unit_norm(matrix):
        norm = np.linalg.norm(matrix)
        if norm == 0:
            return matrix  # Avoid division by zero
        return matrix / norm

    @staticmethod
    def read_obj(file_path):
        vertices = []
        faces = []

        with open(file_path, 'r') as file:
            for line in file:
                parts = line.split()

                if not parts:
                    continue

                if parts[0] == 'v':
                    # Vertex
                    vertex = [float(parts[1]), float(parts[2]), float(parts[3])]
                    vertices.append(vertex)

                elif parts[0] == 'f':
                    # Face
                    face_indices = [int(part.split('/')[0]) for part in parts[1:]]
                    faces.append(face_indices)

        return np.array(vertices), np.array(faces)

    @staticmethod
    def get_inertia_matrix_and_principal_axes(vertices, faces, cm):
        inertia_matrix = np.zeros((3, 3))
        for face in faces:
            triangle_vertices = np.array([vertices[vertex_index - 1] for vertex_index in face])

            for vertex in triangle_vertices:
                deviation = vertex - cm
                inertia_matrix += np.outer(deviation, deviation)

        eigenvalues, eigenvectors = np.linalg.eigh(inertia_matrix)
        sorted_indices = np.argsort(eigenvalues)[::-1]
        eigenvectors = eigenvectors[:, sorted_indices]

        # Eigenvectors represent the principal axes
        principal_axes = eigenvectors.T

        return inertia_matrix, principal_axes

    @staticmethod
    def calculate_moments_along_principal_axes(inertia_tensor, principal_axes):
        rotated_inertia_tensor = np.dot(np.dot(principal_axes.T, inertia_tensor), principal_axes)
        moments_along_axes = np.diag(rotated_inertia_tensor)
        return moments_along_axes

    @staticmethod
    def calculate_avg_distance_and_var_along_principal_axes(vertices, faces, principal_axes, center_of_mass):
        averages_along_axes = [0.0] * 3
        variances_along_axes = [0.0] * 3

        for face in faces:
            vertices_face = vertices[np.array(face) - 1]

            if len(vertices_face) < 3:
                print(f"Error: Face {face} does not have enough vertices.")
                continue

            dA = 0.5 * np.linalg.norm(
                np.cross(vertices_face[1] - vertices_face[0], vertices_face[2] - vertices_face[0]))
            face_center = np.sum(vertices_face, axis=0) / 3.0
            distances_to_axes = np.dot(face_center - center_of_mass, principal_axes.T)

            for i, distance_to_axis in enumerate(distances_to_axes):
                averages_along_axes[i] += abs(distance_to_axis * dA)

        valid_faces_count = sum([0.5 * np.linalg.norm(
            np.cross(vertices[face[1] - 1] - vertices[face[0] - 1], vertices[face[2] - 1] - vertices[face[0] - 1]))
                                 for face in faces])

        if valid_faces_count == 0:
            print("Error: No valid faces found.")
            return [0.0, 0.0, 0.0], [0.0, 0.0, 0.0]

        for i in range(3):
            averages_along_axes[i] /= valid_faces_count

        for face in faces:
            vertices_face = vertices[np.array(face) - 1]

            if len(vertices_face) < 3:
                continue

            dA = 0.5 * np.linalg.norm(
                np.cross(vertices_face[1] - vertices_face[0], vertices_face[2] - vertices_face[0]))
            face_center = np.sum(vertices_face, axis=0) / 3.0
            distances_to_axes = np.dot(face_center - center_of_mass, principal_axes.T)

            for i, distance_to_axis in enumerate(distances_to_axes):
                variances_along_axes[i] += ((abs(distance_to_axis * dA) - averages_along_axes[i]) ** 2)

        for i in range(3):
            variances_along_axes[i] /= valid_faces_count

        return averages_along_axes, variances_along_axes

    @staticmethod
    def normalize_orientation_and_size(vertices, faces, num_points_per_triangle=100):
        # Generate random points on each triangle
        points_on_triangles = MeshFeaturesCalculator.generate_points_on_triangles(vertices, faces,
                                                                                  num_points_per_triangle)

        # Compute the center of mass
        center_of_mass = np.mean(points_on_triangles, axis=0)

        # Compute the covariance matrix
        covariance_matrix = np.cov(points_on_triangles, rowvar=False)

        # Compute the principal axes of inertia
        _, principal_axes = np.linalg.eigh(covariance_matrix)

        # Sort the principal axes by decreasing magnitude of eigenvalues
        sorted_indices = np.argsort(np.linalg.eigvals(covariance_matrix))[::-1]
        principal_axes = principal_axes[:, sorted_indices]

        # Align the model with the principal axes
        aligned_vertices = MeshFeaturesCalculator.align_with_principal_axes(vertices - center_of_mass, principal_axes)

        return aligned_vertices

    @staticmethod
    def generate_points_on_triangles(vertices, faces, num_points_per_triangle):
        points_on_triangles = []

        for face in faces:
            triangle_vertices = vertices[face - 1]

            for _ in range(num_points_per_triangle):
                # Generate random numbers for point placement
                r1, r2 = np.random.uniform(0, 1, size=(2,))

                # Compute the point using the Osada's method
                point = triangle_vertices[0] + r1 * (triangle_vertices[1] - triangle_vertices[0]) + \
                        r2 * (triangle_vertices[2] - triangle_vertices[0])

                points_on_triangles.append(point)

        return np.array(points_on_triangles)

    @staticmethod
    def align_with_principal_axes(vertices, principal_axes):
        # Align vertices with the principal axes using rotation matrix
        rotation_matrix = Rotation.from_matrix(principal_axes.T).as_matrix()
        aligned_vertices = np.dot(vertices, rotation_matrix)

        return aligned_vertices
