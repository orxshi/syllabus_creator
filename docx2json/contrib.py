from docx import Document

def parse_contributions(doc):
    """
    Parse the 'Course’s Contribution to Program' section from a single DOCX table.
    Returns a dict like {"contrib0": "...", "contribval0": "..."}
    """
    table = doc.tables[0]  # single large table

    contrib_rows = []
    in_contrib = False

    for row in table.rows:
        cells = [cell.text.strip() for cell in row.cells]
        if not any(cells):
            continue

        first_cell = cells[0].lower()

        if "course’s contribution" in first_cell:
            in_contrib = True
            continue  # skip heading row

        if in_contrib and first_cell.startswith("cl (contribution level)"):
            break  # stop at legend row

        if in_contrib:
            # Only process numbered rows (first cell is digit)
            if cells[0].isdigit():
                contrib_rows.append(cells)

    # Build JSON
    contrib_json = {}
    for i, row in enumerate(contrib_rows):
        # row[1] or row[2] → text, row[4] → CL value (adjust if needed)
        text = row[1] if row[1] else row[2]
        value = row[4] if len(row) > 4 else ""
        contrib_json[f"contrib{i}"] = text
        contrib_json[f"contribval{i}"] = value

    return contrib_json