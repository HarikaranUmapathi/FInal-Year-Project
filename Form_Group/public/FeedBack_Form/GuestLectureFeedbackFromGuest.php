<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Guest Lecture Feedback</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-serif">

<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow">

  <!-- HEADER -->
  <h2 class="text-center font-bold text-xl">
    GUEST LECTURE FEEDBACK FROM GUEST
  </h2>
  <p class="text-center text-sm mt-1">
    C. Abdul Hakeem College of Engineering & Technology
  </p>

  <!-- FORM -->
  <form id="feedbackForm" class="mt-6 space-y-4">

    <!-- BASIC DETAILS -->
    <div>
      <label for="guestName" class="font-semibold">Name of the Guest</label>
      <input type="text" id="guestName" class="w-full border p-2" required>
    </div>

    <div>
      <label for="designation" class="font-semibold">Designation</label>
      <input type="text" id="designation" class="w-full border p-2" required>
    </div>

    <div>
      <label for="org" class="font-semibold">Organization / Institution</label>
      <input type="text" id="org" class="w-full border p-2" required>
    </div>

    <div>
      <label for="subject" class="font-semibold">Subject</label>
      <input type="text" id="subject" class="w-full border p-2">
    </div>

    <div>
      <label for="objective" class="font-semibold">Objective</label>
      <textarea id="objective" class="w-full border p-2"></textarea>
    </div>

    <!-- EFFECTIVENESS -->
    <p class="font-semibold mt-4">
      Effectiveness (5-Excellent | 1-Poor)
    </p>

    <!-- QUESTIONS -->
    <div class="space-y-4">

      <div>
        <label class="block font-medium">1. Topic relevance</label>
        <div class="flex gap-4">
          <label><input type="radio" name="q1" value="5"> 5</label>
          <label><input type="radio" name="q1" value="4"> 4</label>
          <label><input type="radio" name="q1" value="3"> 3</label>
          <label><input type="radio" name="q1" value="2"> 2</label>
          <label><input type="radio" name="q1" value="1"> 1</label>
        </div>
      </div>

      <div>
        <label class="block font-medium">2. Presentation clarity</label>
        <div class="flex gap-4">
          <label><input type="radio" name="q2" value="5"> 5</label>
          <label><input type="radio" name="q2" value="4"> 4</label>
          <label><input type="radio" name="q2" value="3"> 3</label>
          <label><input type="radio" name="q2" value="2"> 2</label>
          <label><input type="radio" name="q2" value="1"> 1</label>
        </div>
      </div>

      <div>
        <label class="block font-medium">3. Interaction with students</label>
        <div class="flex gap-4">
          <label><input type="radio" name="q3" value="5"> 5</label>
          <label><input type="radio" name="q3" value="4"> 4</label>
          <label><input type="radio" name="q3" value="3"> 3</label>
          <label><input type="radio" name="q3" value="2"> 2</label>
          <label><input type="radio" name="q3" value="1"> 1</label>
        </div>
      </div>

      <div>
        <label class="block font-medium">4. Overall effectiveness</label>
        <div class="flex gap-4">
          <label><input type="radio" name="q4" value="5"> 5</label>
          <label><input type="radio" name="q4" value="4"> 4</label>
          <label><input type="radio" name="q4" value="3"> 3</label>
          <label><input type="radio" name="q4" value="2"> 2</label>
          <label><input type="radio" name="q4" value="1"> 1</label>
        </div>
      </div>

    </div>

    <!-- SUBMIT -->
    <button type="submit"
      class="mt-6 bg-blue-600 text-white px-6 py-2 rounded">
      Submit Feedback
    </button>

  </form>
</div>

<!-- RESULTS -->
<div class="max-w-4xl mx-auto bg-white p-6 mt-6 shadow hidden" id="resultBox">

  <!-- RESPONSE COUNT -->
  <p class="font-semibold mb-4">
    Total Responses Submitted:
    <span id="responseCount" class="text-blue-600">0</span>
  </p>

  <!-- AVERAGE GRAPH -->
  <h3 class="font-semibold mb-2">Average Feedback Graph</h3>
  <canvas id="avgChart"></canvas>
</div>

<!-- JAVASCRIPT -->
<script>
const responses = [];
let chart;

document.getElementById("feedbackForm").addEventListener("submit", function(e) {
  e.preventDefault();

  let current = [];

  for (let i = 1; i <= 4; i++) {
    const selected = document.querySelector(`input[name="q${i}"]:checked`);
    if (!selected) {
      alert("Please answer all questions");
      return;
    }
    current.push(parseInt(selected.value));
  }

  responses.push(current);

  // Update response count
  document.getElementById("responseCount").innerText = responses.length;
  document.getElementById("resultBox").classList.remove("hidden");

  // Calculate averages
  const averages = [];
  for (let q = 0; q < 4; q++) {
    let sum = 0;
    responses.forEach(r => sum += r[q]);
    averages.push((sum / responses.length).toFixed(2));
  }

  // Draw / Update chart
  if (chart) chart.destroy();

  chart = new Chart(document.getElementById("avgChart"), {
    type: "bar",
    data: {
      labels: [
        "Topic Relevance",
        "Presentation Clarity",
        "Student Interaction",
        "Overall Effectiveness"
      ],
      datasets: [{
        label: "Average Rating",
        data: averages,
        backgroundColor: "rgba(59,130,246,0.6)"
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          max: 5,
          ticks: { stepSize: 1 }
        }
      }
    }
  });

  // Reset only ratings
  for (let i = 1; i <= 4; i++) {
    document.querySelectorAll(`input[name="q${i}"]`).forEach(r => r.checked = false);
  }
});
</script>

</body>
</html>
