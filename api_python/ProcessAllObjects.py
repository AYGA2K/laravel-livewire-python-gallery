import json
import os
from ProcessObject import MeshFeaturesCalculator


class ProcessDirectory:
    @staticmethod
    def process_directory(directory_path, partial_results):
        # Iterate through files in the directory
        for filename in os.listdir(directory_path):
            if filename.endswith(".obj") and filename not in partial_results:
                file_path = os.path.join(directory_path, filename)

                try:
                    features = MeshFeaturesCalculator.calculate_features(file_path)
                    print(filename)

                    # Add results to the dictionary, using the filename as key
                    partial_results[filename] = features
                except Exception as e:
                    print(f"Error processing {filename}: {e}")
                    # Skip to the next file in case of an error

        return partial_results

    @staticmethod
    def process_directories(root_directory):
        all_results = {}

        # Iterate through subdirectories
        for subdirectory in os.listdir(root_directory):
            subdirectory_path = os.path.join(root_directory, subdirectory)

            if os.path.isdir(subdirectory_path):
                # Process each subdirectory
                subdirectory_results = ProcessDirectory.process_directory(
                    subdirectory_path, all_results.get(subdirectory, {})
                )

                # Add results to the dictionary for all directories
                all_results[subdirectory] = subdirectory_results

        return all_results

    @staticmethod
    def read_results_from_json(json_filename):
        with open(json_filename, "r") as json_file:
            results = json.load(json_file)

        # Use a dictionary comprehension for a more concise structure
        extracted_features = {
            entry_name: {
                obj_name: {
                    "inertia_matrix": obj_data.get("inertia_matrix"),
                    "moment_along_principal_axes": obj_data.get(
                        "moment_along_principal_axes"
                    ),
                    "averages_along_axes": obj_data.get("averages_along_axes"),
                    "variances_along_axes": obj_data.get("variances_along_axes"),
                }
                for obj_name, obj_data in entry_data.items()
            }
            for entry_name, entry_data in results.items()
        }

        return extracted_features

    @staticmethod
    def process_and_save(root_directory, json_filename="all_results.json"):
        all_results = ProcessDirectory.process_directories(root_directory)
        with open(json_filename, "w") as json_file:
            json.dump(all_results, json_file, indent=2)

        print(f"All results saved to {json_filename}")
