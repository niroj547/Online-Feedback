// Simple validation for feedback_form.html
function validateForm() {
    const name = document.forms["feedbackForm"]["studentName"].value;
    const date = document.forms["feedbackForm"]["feedbackDate"].value;
  
    if (name === "" || date === "") {
      alert("Please fill all required fields.");
      return false;
    }
    return true;
  }
  