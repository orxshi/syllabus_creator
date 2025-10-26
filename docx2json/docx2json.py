import os
import json
from docx import Document
from basics import parse_basics
from objectives import parse_objectives
from outcomes import parse_outcomes
from contrib import parse_contributions
from contents import parse_contents
from sources import parse_sources
from assess import parse_assessment
from ects import parse_ects

def convert_docx_to_json():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    docx_folder = os.path.join(base_dir, "docx")
    json_folder = os.path.join(base_dir, "json")

    # Create json folder if it doesn't exist
    os.makedirs(json_folder, exist_ok=True)

    for file in os.listdir(docx_folder):
        if file.lower().endswith(".docx"):
            docx_path = os.path.join(docx_folder, file)
            json_path = os.path.join(json_folder, os.path.splitext(file)[0] + ".json")
            try:
                doc = Document(docx_path)

                # Parse all sections
                json_data = {}
                json_data.update(parse_basics(doc))
                json_data.update(parse_objectives(doc))
                json_data.update(parse_outcomes(doc))
                json_data.update(parse_contributions(doc))
                json_data.update(parse_contents(doc))
                json_data.update(parse_sources(doc))
                json_data.update(parse_assessment(doc))
                json_data.update(parse_ects(doc))

                # Save JSON
                with open(json_path, "w", encoding="utf-8") as f:
                    json.dump(json_data, f, indent=4, ensure_ascii=False)

                print(f"✅ Converted: {file} -> {json_path}")

            except Exception as e:
                print(f"❌ Failed to convert {file}: {e}")

if __name__ == "__main__":
    convert_docx_to_json()
