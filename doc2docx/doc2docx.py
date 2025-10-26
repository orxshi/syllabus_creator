import win32com.client as win32
import os

def convert_all_docs():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    doc_folder = os.path.join(base_dir, "doc")
    docx_folder = os.path.join(base_dir, "docx")

    # Create docx folder if it doesn't exist
    os.makedirs(docx_folder, exist_ok=True)

    # Start Word once
    word = win32.gencache.EnsureDispatch('Word.Application')
    word.Visible = False

    for file in os.listdir(doc_folder):
        if file.lower().endswith(".doc") and not file.lower().endswith(".docx"):
            doc_path = os.path.join(doc_folder, file)
            docx_path = os.path.join(docx_folder, os.path.splitext(file)[0] + ".docx")
            try:
                doc = word.Documents.Open(doc_path)
                doc.SaveAs2(docx_path, FileFormat=16)
                doc.Close()
                print(f"✅ Converted: {file} -> {docx_path}")
            except Exception as e:
                print(f"❌ Failed to convert {file}: {e}")

    word.Quit()


if __name__ == "__main__":
    convert_all_docs()
