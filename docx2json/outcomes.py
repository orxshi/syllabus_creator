from docx import Document

def parse_outcomes(doc):
    """
    Parse the 'Learning Outcomes' section from a single DOCX table.
    Returns a dict like {"out0": "...", "outval0": "..."}
    """
    table = doc.tables[0]  # single large table

    outcomes_rows = []
    in_outcomes = False

    for row in table.rows:
        # Get first non-empty cell values
        cells = [cell.text.strip() for cell in row.cells]
        if not any(cells):
            continue

        first_cell = cells[0].lower()

        if "learning outcomes" in first_cell:
            in_outcomes = True
            continue  # skip heading

        # Stop if next section starts
        if in_outcomes and "course’s contribution" in first_cell.lower():
            break  # stop at Contributions

        if in_outcomes:
            # Only process numbered rows (first cell is digit)
            if cells[0].isdigit():
                outcomes_rows.append(cells)

    # Build JSON
    out_json = {}
    for i, row in enumerate(outcomes_rows):
        # row[1] → main outcome text
        # row[3] or row[4] → associated values (depends on DOCX)
        outcome_text = row[1]
        outcome_val = row[3] if len(row) > 3 else ""
        out_json[f"out{i}"] = outcome_text
        out_json[f"outval{i}"] = outcome_val

    return out_json