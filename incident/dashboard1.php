<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Incident Tracker Dashboard</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .header {
      background-color: #4CAF50;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .container {
      display: flex;
      justify-content: space-between;
      margin: 20px;
    }

    .left-sidebar {
      width: 20%;
      background-color: #f9f9f9;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .right-section {
      width: 75%;
      background-color: white;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .incident-card {
      background-color: #fff;
      border: 1px solid #ddd;
      margin: 10px 0;
      padding: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .incident-card h4 {
      margin-top: 0;
    }

    .incident-card .status {
      padding: 5px;
      border-radius: 5px;
    }

    .status.open {
      background-color: #ffcc00;
    }

    .status.in-progress {
      background-color: #ff6600;
    }

    .status.closed {
      background-color: #4CAF50;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table, th, td {
      border: 1px solid #ddd;
    }

    th, td {
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .incident-form {
      display: flex;
      flex-direction: column;
    }

    .incident-form input,
    .incident-form select,
    .incident-form button {
      padding: 10px;
      margin: 5px 0;
    }

    .incident-form button {
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>Incident Tracker Dashboard</h1>
  </div>

  <div class="container">

    <!-- Left Sidebar -->
    <div class="left-sidebar">
      <h3>Filter Incidents</h3>
      <form class="incident-form">
        <label for="status">Status:</label>
        <select id="status">
          <option value="all">All</option>
          <option value="open">Open</option>
          <option value="in-progress">In Progress</option>
          <option value="closed">Closed</option>
        </select>

        <label for="priority">Priority:</label>
        <select id="priority">
          <option value="all">All</option>
          <option value="high">High</option>
          <option value="medium">Medium</option>
          <option value="low">Low</option>
        </select>

        <button type="submit">Apply Filters</button>
      </form>
    </div>

    <!-- Right Section (Main Dashboard) -->
    <div class="right-section">

      <!-- Incident List -->
      <h3>Incident List</h3>
      <div class="incident-card">
        <h4>Incident #001 - Network Issue</h4>
        <p><strong>Priority:</strong> High</p>
        <p><strong>Description:</strong> Network downtime in Region A.</p>
        <p><strong>Assigned To:</strong> John Doe</p>
        <p class="status open">Status: Open</p>
      </div>

      <div class="incident-card">
        <h4>Incident #002 - Server Crash</h4>
        <p><strong>Priority:</strong> Medium</p>
        <p><strong>Description:</strong> The application server is down.</p>
        <p><strong>Assigned To:</strong> Jane Smith</p>
        <p class="status in-progress">Status: In Progress</p>
      </div>

      <div class="incident-card">
        <h4>Incident #003 - Database Corruption</h4>
        <p><strong>Priority:</strong> High</p>
        <p><strong>Description:</strong> Database corruption affecting multiple services.</p>
        <p><strong>Assigned To:</strong> Alice Brown</p>
        <p class="status closed">Status: Closed</p>
      </div>

      <!-- Incident Table -->
      <h3>Incident Table</h3>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Date Reported</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>#001</td>
            <td>Network Issue</td>
            <td>High</td>
            <td>Open</td>
            <td>John Doe</td>
            <td>2024-12-01</td>
          </tr>
          <tr>
            <td>#002</td>
            <td>Server Crash</td>
            <td>Medium</td>
            <td>In Progress</td>
            <td>Jane Smith</td>
            <td>2024-12-03</td>
          </tr>
          <tr>
            <td>#003</td>
            <td>Database Corruption</td>
            <td>High</td>
            <td>Closed</td>
            <td>Alice Brown</td>
            <td>2024-12-05</td>
          </tr>
        </tbody>
      </table>

    </div>
  </div>

</body>
</html>
