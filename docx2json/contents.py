from docx import Document

def parse_contents(doc):
    """
    Parse the 'Course Contents' from the second table in the DOCX.
    Only keep weekly course contents (weeks 1-15), ignore headers and subsequent sections.
    """
    table = doc.tables[1]  # second table
    contents_json = {}
    index = 0  # JSON key index

    for row in table.rows[1:]:  # skip header row
        cells = [cell.text.strip() for cell in row.cells]
        if len(cells) < 3:
            continue

        col0 = cells[0]
        col2 = cells[2]
        col8 = cells[8] if len(cells) > 8 else ""

        # Skip headers
        if col0.lower() == "week" or col8.lower() == "exams":
            continue

        # Stop parsing if col0 is non-empty and not a week number (Recommended Sources, Assessment, etc.)
        if col0 and not col0.isdigit():
            break

        # Skip week numbers
        if col0.isdigit():
            col0 = ""

        # Only store meaningful rows
        if col0 or col2 or col8:
            contents_json[f"conchp{index}"] = col0
            contents_json[f"consub{index}"] = col2
            contents_json[f"conlab{index}"] = col8
            index += 1

    return contents_json