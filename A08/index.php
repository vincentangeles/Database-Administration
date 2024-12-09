<?php
include("connect.php");

// Initialize variables for filters
$searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'departureAirportCode'; // Default order by column
$orderDir = isset($_GET['orderDir']) && $_GET['orderDir'] === 'DESC' ? 'DESC' : 'ASC'; // Default direction

// Build the query
$query = "SELECT * FROM flightLogs";

// Add search term filter if provided
if (!empty($searchTerm)) {
  $query .= " WHERE 
                flightNumber LIKE '%$searchTerm%' OR 
                departureAirportCode LIKE '%$searchTerm%' OR 
                arrivalAirportCode LIKE '%$searchTerm%' OR 
                airlineName LIKE '%$searchTerm%' OR 
                aircraftType LIKE '%$searchTerm%' OR 
                pilotName LIKE '%$searchTerm%' OR 
                departureDatetime LIKE '%$searchTerm%' OR 
                arrivalDatetime LIKE '%$searchTerm%' OR 
                flightDurationMinutes LIKE '%$searchTerm%' OR 
                passengerCount LIKE '%$searchTerm%' OR 
                ticketPrice LIKE '%$searchTerm%' OR 
                creditCardNumber LIKE '%$searchTerm%' OR 
                creditCardType LIKE '%$searchTerm%'";
}

// Add order by and sort direction
$query .= " ORDER BY $orderBy $orderDir";

// Execute the query
$result = executeQuery($query);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FLY HIGH PUP</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <div class="container mt-5">
    <!-- Title -->
    <div class="row mb-4">
      <div class="col text-center">
        <h1 class="display-4 fw-bold">FLY HIGH PUP</h1>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="row py-3">
      <div class="col">
        <form method="GET" class="text-center">
          <div class="mb-3">
            <input type="text" class="p-3 shadow-sm rounded-5 form-control" placeholder="Search across all fields"
              name="searchTerm" value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>">
          </div>
          <div class="d-flex flex-row justify-content-center">
            <!-- Search Button -->
            <button type="submit" class="rounded-5 p-3 btn btn-primary me-2">Search</button>
            <!-- Clear Button -->
            <a href="index.php" class="rounded-5 p-3 btn btn-secondary me-2">Clear</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Sort and Order Dropdowns -->
    <div class="row py-3">
      <div class="col text-center">
        <form method="GET">
          <!-- Hidden input to preserve search term -->
          <input type="hidden" name="searchTerm"
            value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>">

          <!-- Sort By Dropdown -->
          <div class="btn-group me-2" role="group" aria-label="Sort By Dropdown">
            <select class="form-select" name="orderBy" onchange="this.form.submit()">
              <option value="flightNumber" <?php echo $orderBy === 'flightNumber' ? 'selected' : ''; ?>>Flight Number
              </option>
              <option value="departureAirportCode" <?php echo $orderBy === 'departureAirportCode' ? 'selected' : ''; ?>>
                Departure Airport</option>
              <option value="arrivalAirportCode" <?php echo $orderBy === 'arrivalAirportCode' ? 'selected' : ''; ?>>
                Arrival Airport</option>
              <option value="airlineName" <?php echo $orderBy === 'airlineName' ? 'selected' : ''; ?>>Airline</option>
              <option value="ticketPrice" <?php echo $orderBy === 'ticketPrice' ? 'selected' : ''; ?>>Ticket Price
              </option>
            </select>
          </div>

          <!-- Order By Dropdown -->
          <div class="btn-group" role="group" aria-label="Order Dropdown">
            <select class="form-select" name="orderDir" onchange="this.form.submit()">
              <option value="ASC" <?php echo $orderDir === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
              <option value="DESC" <?php echo $orderDir === 'DESC' ? 'selected' : ''; ?>>Descending</option>
            </select>
          </div>
        </form>
      </div>
    </div>

    <!-- Results Table -->
    <div class="row">
      <div class="col">
        <div class="card p-3 shadow-sm">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="table-light">
                <tr>
                  <th scope="col">Flight Number</th>
                  <th scope="col">Departure Airport</th>
                  <th scope="col">Arrival Airport</th>
                  <th scope="col">Departure Time</th>
                  <th scope="col">Arrival Time</th>
                  <th scope="col">Duration (Minutes)</th>
                  <th scope="col">Airline</th>
                  <th scope="col">Aircraft Type</th>
                  <th scope="col">Passenger Count</th>
                  <th scope="col">Ticket Price</th>
                  <th scope="col">Credit Card Number</th>
                  <th scope="col">Credit Card Type</th>
                  <th scope="col">Pilot Name</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($flight = mysqli_fetch_assoc($result)) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($flight['flightNumber'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['departureAirportCode'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['arrivalAirportCode'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['departureDatetime'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['arrivalDatetime'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['flightDurationMinutes'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['airlineName'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['aircraftType'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['passengerCount'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['ticketPrice'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['creditCardNumber'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['creditCardType'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($flight['pilotName'], ENT_QUOTES, 'UTF-8'); ?></td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5pNDFVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>