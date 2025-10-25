from docx import Document
import re

def parse_objectives(doc):
    """
    Parse the 'Objectives of the Course' section from a single DOCX table.
    Returns a dict like {"obj0": "...", "obj1": "..."}
    """
    table = doc.tables[0]  # single large table

    # Helper: remove consecutive duplicates
    def clean_row(row):
        texts = [cell.text.strip() for cell in row.cells if cell.text.strip()]
        cleaned = []
        for t in texts:
            if not cleaned or t != cleaned[-1]:
                cleaned.append(t)
        return cleaned

    # Locate Objectives section
    objectives_rows = []
    in_objectives = False
    for row in table.rows:
        cleaned = clean_row(row)
        if not cleaned:
            continue

        first_cell = cleaned[0].lower()

        if "objectives" in first_cell:
            in_objectives = True
            continue  # skip heading row

        # Stop if another section starts
        if in_objectives and ("learning outcomes" in first_cell or "course’s contribution" in first_cell):
            break

        if in_objectives:
            objectives_rows.append(cleaned)

    # Extract text from first meaningful row
    obj_text = ""
    for row in objectives_rows:
        if len(set(row)) == 1 and "objectives" in row[0].lower():
            continue
        obj_text = row[2] if len(row) >= 3 else row[0]
        break

    # Split text into individual objectives
    # Split by line breaks, bullet characters, numbered lists
    split_pattern = r'[\n\u2022\u2023\u25E6\u2043\u2219\uF0D8]|(?:\d+\.)'
    raw_lines = [line.strip() for line in re.split(split_pattern, obj_text) if line.strip()]

    # Clean up any remaining tabs or bullet-like characters
    lines = [re.sub(r'^[\t•\uf0d8\s]+', '', line) for line in raw_lines]

    # Build JSON dynamically
    obj_json = {f"obj{i}": line for i, line in enumerate(lines)}

    return obj_json