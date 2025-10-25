import win32com.client as win32
import os

def doc_to_docx(filename):
    # Always work in the current folder (where the script is located)
    current_dir = os.path.dirname(os.path.abspath(__file__))
    abs_path = os.path.join(current_dir, filename)

    if not os.path.exists(abs_path):
        raise FileNotFoundError(f"File not found: {abs_path}")

    word = win32.gencache.EnsureDispatch('Word.Application')
    word.Visible = False  # keep Word hidden

    doc = word.Documents.Open(abs_path)
    docx_path = os.path.splitext(abs_path)[0] + ".docx"
    doc.SaveAs2(docx_path, FileFormat=16)  # 16 = wdFormatXMLDocument

    doc.Close()
    word.Quit()

    print(f"✅ Converted successfully: {docx_path}")

# Example
doc_to_docx("CEN301.doc")
