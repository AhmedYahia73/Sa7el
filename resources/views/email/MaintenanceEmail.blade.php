<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Maintenance Request Notification</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f6f8fa;
      margin: 0;
      padding: 0;
    }
    .email-container {
      max-width: 600px;
      margin: auto;
      background-color: #ffffff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .header {
      background-color: #2a9d8f;
      color: #ffffff;
      padding: 20px;
      text-align: center;
    }
    .content {
      padding: 30px;
      color: #333333;
    }
    .content h2 {
      margin-top: 0;
    }
    .footer {
      background-color: #f1f1f1;
      padding: 15px;
      text-align: center;
      font-size: 13px;
      color: #666666;
    }
    .button {
      display: inline-block;
      margin-top: 20px;
      padding: 12px 20px;
      background-color: #2a9d8f;
      color: #ffffff;
      text-decoration: none;
      border-radius: 5px;
    }
    .details-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .details-table td {
      padding: 10px;
      border-bottom: 1px solid #eee;
    }
    .details-table td.label {
      font-weight: bold;
      width: 30%;
      background-color: #f9f9f9;
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="header">
      <h1>Maintenance Request Submitted</h1>
    </div>
    <div class="content">
      <h2>Hello Village Admin,</h2>
      <p>A resident has submitted a new maintenance request. Below are the details:</p>

      <table class="details-table">
        <tr>
          <td class="label">Name:</td>
          <td>{{ $maintenance['user']['name'] }}</td>
        </tr>
        <tr>
          <td class="label">Phone:</td>
          <td>{{ $maintenance['user']['phone'] }}</td>
        </tr>
        <tr>
          <td class="label">Unit:</td>
          <td>{{ $maintenance['appartment']['unit'] }}</td>
        </tr>
        <tr>
          <td class="label">Maintenance Type:</td>
          <td>{{ $maintenance['maintenance_type']['name'] }}</td>
        </tr>
        <tr>
          <td class="label">Request Date:</td>
          <td>{{ $maintenance['appartment']['created_at'] }}</td>
        </tr>
        <tr>
          <td class="label">Issue:</td>
          <td>{{ $maintenance['description'] }}</td>
        </tr>
      </table>
 
    </div>
    <div class="footer">
      &copy; {{ date('Y-m-d') }} Seago. All rights reserved.
    </div>
  </div>
</body>
</html>
