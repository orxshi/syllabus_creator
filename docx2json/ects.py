from docx import Document

def parse_ects(doc):
    """
    Parse ECTS allocation (Activities, Number, Duration) from Table 2 of DOCX.
    Ignores total rows like 'Total Workload', 'Total Workload/30 (h)', and 'ECTS Credit of the Course'.
    Returns a dict suitable for JSON conversion.
    """
    table = doc.tables[1]  # second table
    ects_json = {}
    index = 1
    found_header = False

    for row in table.rows:
        first_cell_text = row.cells[0].text.strip().lower()
        
        # Start processing after the 'Activities' header row
        if 'activities' in first_cell_text and not found_header:
            found_header = True
            continue

        if found_header:
            # Stop processing if we reach total/summary rows
            if 'total workload' in first_cell_text or 'ects credit' in first_cell_text:
                break

            # Extract columns safely
            act = row.cells[0].text.strip()
            num = row.cells[5].text.strip() if len(row.cells) > 5 else ""
            dur = row.cells[7].text.strip() if len(row.cells) > 7 else ""

            # Skip completely empty rows
            if not act and not num and not dur:
                continue

            # Store in dict with incremented index
            ects_json[f"ectsact{index}"] = act
            ects_json[f"ectsnm{index}"] = num
            ects_json[f"ectsdur{index}"] = dur
            index += 1

    return ects_json
