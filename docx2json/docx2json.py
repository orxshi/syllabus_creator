from docx import Document
import json
from basics import parse_basics
from objectives import parse_objectives
from outcomes import parse_outcomes
from contrib import parse_contributions
from contents import parse_contents
from sources import parse_sources
from assess import parse_assessment
from ects import parse_ects

# Load docx
doc = Document("CEN301.docx")

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

# Save single JSON
with open("CEN301.json", "w") as f:
    json.dump(json_data, f, indent=4)

# # Iterate over all tables
# for t_index, table in enumerate(doc.tables):
#     print(f"\nTable {t_index + 1}: {len(table.rows)} rows, {len(table.columns)} columns")
    
#     # Iterate over rows
#     for r_index, row in enumerate(table.rows):
#         row_text = [cell.text.strip() for cell in row.cells]
#         print(f" Row {r_index + 1}: {row_text}")