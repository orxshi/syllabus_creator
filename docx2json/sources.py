from docx import Document

def parse_sources(doc):
    """
    Parse Recommended Sources from Table 2 of the DOCX.
    Return JSON with unique sources only.
    """
    table = doc.tables[1]  # Table 2
    sources_set = set()  # to remove duplicates
    sources_json = {}
    index = 0
    found_header = False

    for row in table.rows:
        first_cell_text = row.cells[0].text.strip()
        
        if first_cell_text.lower() == "recommended sources":
            found_header = True
            continue
        
        if found_header:
            # Combine all cells in the row
            combined_text = " ".join([cell.text.strip() for cell in row.cells if cell.text.strip()])
            # Split by line breaks
            lines = combined_text.split("\n")
            for line in lines:
                line = line.strip()
                # Ignore empty lines and "Supplementary Material" labels
                if not line or "Supplementary Material" in line:
                    continue
                # Remove trailing commas
                line = line.rstrip(",")
                # Add to set to avoid duplicates
                sources_set.add(line)
            break  # only first row after header contains sources

    # Convert set to JSON with numbered keys
    for s in sources_set:
        sources_json[f"source{index}"] = s
        index += 1

    return sources_json