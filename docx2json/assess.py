from docx import Document

def parse_assessment(doc):
    """
    Parse Assessment table from DOCX.
    Return a JSON-like dictionary with activities and their percentages (without % sign).
    Ignores the 'Total' row.
    """
    table = doc.tables[1]  # second table
    assessment_json = {}
    index = 0
    found_header = False

    for row in table.rows:
        first_cell_text = row.cells[0].text.strip().lower()
        
        # Skip until we find the "Assessment" header
        if first_cell_text == "assessment":
            found_header = True
            continue

        if found_header:
            # Stop if we reach 'Total'
            if first_cell_text == "total":
                break

            # Collect unique non-empty activity names from first 3 columns
            activity_parts = []
            for i in range(3):
                text = row.cells[i].text.strip()
                if text and text not in activity_parts:
                    activity_parts.append(text)
            activity = " and ".join(activity_parts)

            # Take percentage from the 4th column (index 3)
            if len(row.cells) > 3:
                percent = row.cells[3].text.strip()
                percent = percent.replace("%", "").strip()  # remove percent sign
            else:
                percent = ""

            assessment_json[f"act{index}"] = activity
            assessment_json[f"actper{index}"] = percent
            index += 1

    return assessment_json
