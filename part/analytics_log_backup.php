<?php
// Include the database connection
include('connect.php');

// Check if 'section' parameter is set in the URL
$section = isset($_GET['section']) ? $_GET['section'] : '';

// Determine the type based on the section
$typeCondition = '';
if ($section === 'student_log') {
    $typeCondition = "WHERE type = 'student'";
} elseif ($section === 'faculty_log') {
    $typeCondition = "WHERE type = 'faculty'";
} else{
    $isLogHistory = isset($_GET['isLogHistory']) ? $_GET['isLogHistory'] : false;
$typeCondition = $isLogHistory ? "" : "WHERE type = '$userType'"; // Assuming $userType is defined appropriately
}

// Query to get distinct rooms and their counts
$queryRooms = "SELECT room, COUNT(*) as count FROM records_log $typeCondition GROUP BY room ASC";
$resultRooms = $conn->query($queryRooms);

$rooms = [];
$roomColors = []; // Define an array for colors
$totalCountRooms = 0;

// Fetch the room counts
if ($resultRooms->num_rows > 0) {
    while ($row = $resultRooms->fetch_assoc()) {
        $rooms[$row['room']] = $row['count'];
        $totalCountRooms += $row['count'];
    }

    // Find the highest and lowest counts for rooms
    $highestCountRooms = max($rooms);
    $lowestCountRooms = min($rooms);

    // Assign colors based on the counts
    foreach ($rooms as $room => $count) {
        if ($count === $highestCountRooms) {
            $roomColors[$room] = 'rgba(75, 192, 192, 1)'; // Green for highest
        } elseif ($count === $lowestCountRooms) {
            $roomColors[$room] = 'rgba(255, 99, 132, 1)'; // Red for lowest
        } else {
            $roomColors[$room] = generateUniqueRandomLightColor();
        }
    }
} else {
    $rooms = ['No data' => 0];
    $totalCountRooms = 0;
}

// Query to get distinct pc_id and their counts
$queryPCs = "SELECT pc_id, COUNT(*) as count FROM records_log $typeCondition GROUP BY pc_id ASC";
$resultPCs = $conn->query($queryPCs);

$pcCounts = [];
$pcColors = [];
$totalCountPCs = 0;

// Fetch the pc_id counts
if ($resultPCs->num_rows > 0) {
    while ($row = $resultPCs->fetch_assoc()) {
        $pcCounts[$row['pc_id']] = $row['count'];
        $pcColors[$row['pc_id']] = generateUniqueRandomLightColor(); // Generate unique color for each pc_id
        $totalCountPCs += $row['count'];
    }
} else {
    $pcCounts = ['No data' => 0];
    $pcColors = ['No data' => generateRandomLightColor()];
    $totalCountPCs = 0;
}

// Close the database connection
$conn->close();

// Function to generate random light color
function generateRandomLightColor() {
    $r = rand(200, 255);
    $g = rand(200, 255);
    $b = rand(200, 255);
    return "rgba($r, $g, $b, 1)";
}

// Function to generate unique random light color
function generateUniqueRandomLightColor() {
    $existingColors = $_SESSION['roomColors'] ?? [];
    do {
        $color = generateRandomLightColor();
    } while (in_array($color, $existingColors));
    return $color;
}
?>


<div style="display: flex; height: 250px; margin-top: 1%;margin-left: 1%; margin-right: 1%;">
    <!-- Left side: Room count doughnut chart -->
    <div class="card card-flush mb-5 mb-xl-10" style="width: 25%; height: 100%; margin-right: 2px;">
        <div class="card-header pt-5">
            <div class="card-title d-flex flex-column">
                <div class="d-flex align-items-center">
                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2"><?php echo number_format($totalCountRooms); ?></span>
                    <span class="fs-4 fw-semibold me-1 align-self-start">📝</span>
                </div>
                <span class="text-gray-500 pt-1 fw-semibold fs-6">Logs Count by Room</span>
            </div>
        </div>
        <div class="card-body pt-2 pb-4 d-flex align-items-center" style="height: 100%;">
            <div class="d-flex flex-center me-5 pt-2" style="width: 100%; height: 100%;">
                <form style="width: 100%; height: 100%;">
                    <canvas id="roomChart" style="width: 100%;display: flex; height: 100%; position: relative;"></canvas>
                </form>
            </div>
        </div>
    </div>

    <!-- Right side: PC count bar chart -->
    <div class="card card-flush mb-5 mb-xl-10" style="width: 75%; height: 100%; margin-left: 2px;">
        <div class="card-header pt-5">
            <div class="card-title d-flex flex-column">
                <div class="d-flex align-items-center">
                    <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2"><?php echo number_format($totalCountPCs); ?></span>
                    <span class="fs-4 fw-semibold me-1 align-self-start">
                        <img width="23" height="23" src="workstation.png" alt="workstation"/>
                    </span>
                </div>
                <span class="text-gray-500 pt-1 fw-semibold fs-6">
                    <?php 
                        if (!empty($selectedRoom)) {
                            echo $selectedRoom;
                        }
                        else{
                            echo "Overall";
                        }
                    ?>
                    : PC Logs
                </span>
            </div>
        </div>
        <div class="card-body pt-2 pb-4 d-flex align-items-center" style="height: 100%;">
            <div class="d-flex flex-center me-5 pt-2" style="width: 100%; height: 100%;">
                <canvas id="pcChart" style="width: 100%; height: 100%; position: relative;"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="assets/chart/chart.js"></script>
<script src="assets/chart/chartjs-plugin-datalabels.js"></script>


<script>
// Room chart data
var roomCtx = document.getElementById('roomChart').getContext('2d');
var totalRoomCount = <?php echo $totalCountRooms; ?>; // Fetch the total count from PHP
var roomData = {
    labels: <?php echo json_encode(array_keys($rooms)); ?>, 
    datasets: [{
        label: 'Room Count',
        data: <?php echo json_encode(array_values($rooms)); ?>,
        backgroundColor: <?php echo json_encode(array_values($roomColors)); ?>,
        borderColor: 'rgba(0, 0, 0, 0.1)',
        borderWidth: 1,
    }]
};

// Create the doughnut chart for rooms
var roomChart = new Chart(roomCtx, {
    type: 'doughnut',
    data: roomData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        const dataValue = tooltipItem.raw;
                        return tooltipItem.label + ': ' + dataValue;
                    }
                }
            },
            legend: {
                display: true,
                position: 'right',
            },
            datalabels: {
                anchor: 'center',
                align: 'center',
                formatter: function(value) {
                    const percentage = Math.round((value / totalRoomCount) * 100);
                    return percentage + '%';
                },
                color: 'black',
                font: {
                    weight: 'plain',
                    size: 12
                }
            }
        },
    },
    plugins: [ChartDataLabels]
});

// PC chart data
var pcCtx = document.getElementById('pcChart').getContext('2d');
var pcData = {
    labels: <?php echo json_encode(array_keys($pcCounts)); ?>,
    datasets: [{
        label: 'PC Count',
        data: <?php echo json_encode(array_values($pcCounts)); ?>,
        backgroundColor: <?php echo json_encode(array_values($pcColors)); ?>,
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1,
    }]
};

// Create the bar chart for PC counts with data labels
var pcChart = new Chart(pcCtx, {
    type: 'bar',
    data: pcData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false, // Disable legend display
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        const dataValue = tooltipItem.raw; // The value for the current segment (count)
                        return tooltipItem.label + ': ' + dataValue; // Display label and count only
                    }
                }
            },
            datalabels: {
                display: true, // Enable data labels
                color: 'black', // Color of the count text
                align: 'center',  // Align the labels at the top of the bars
                anchor: 'center', // Anchor the labels to the end of the bars
                formatter: function(value, context) {
                    return value; // Display the PC count as the label
                },
                font: {
                    weight: 'bold', // Set font weight to bold for visibility
                    size: 12 // Set font size if needed
                }
            }
        },
        onClick: function(evt) {
            const activePoints = pcChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
            if (activePoints.length > 0) {
                const firstPoint = activePoints[0];
                const label = pcChart.data.labels[firstPoint.index];
                const value = pcChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
                alert(`PC: ${label}, Logs Count: ${value}`);
            }
        }
    },
    plugins: [ChartDataLabels]
});
</script>



