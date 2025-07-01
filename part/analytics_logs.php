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
$roomQuers = "SELECT DISTINCT room FROM records_log ORDER BY room ASC";
$roomresults = $conn->query($roomQuers);
$rooms = [];
if ($roomresults && $roomresults->num_rows > 0) {
    while ($row = $roomresults->fetch_assoc()) {
        $rooms[] = $row['room'];
    }
}

$roomselected2 = isset($_GET['room']) ? $conn->real_escape_string($_GET['room']) : '';

// Query to get room counts for the doughnut chart (all rooms)
$countroomssss = [];
$roomstotalcounts = 0;
$querycountroomssss = "SELECT room, COUNT(*) as count FROM records_log GROUP BY room ORDER BY room ASC";
$roomresultCounts = $conn->query($querycountroomssss);
if ($roomresultCounts && $roomresultCounts->num_rows > 0) {
    while ($row = $roomresultCounts->fetch_assoc()) {
        $countroomssss[$row['room']] = $row['count'];
        $roomstotalcounts += $row['count'];
    }
}

$countpc2 = [];
$totalcountpc2s = 0;

if ($roomselected2) {
    // Prepared statement to fetch PC counts for the selected room
    $queryPCs = $conn->prepare("SELECT pc_id, COUNT(*) as count FROM records_log WHERE room = ? GROUP BY pc_id ORDER BY pc_id ASC");
    $queryPCs->bind_param("s", $roomselected2);
    $queryPCs->execute();
    $resultPCs = $queryPCs->get_result();

    if ($resultPCs && $resultPCs->num_rows > 0) {
        while ($row = $resultPCs->fetch_assoc()) {
            $countpc2[$row['pc_id']] = $row['count'];
            $totalcountpc2s += $row['count'];
        }
    } else {
        $countpc2 = ['No data' => 0];
    }
} else {
    foreach ($countroomssss as $room => $count) {
        $pcallrooms = $conn->prepare("SELECT pc_id, COUNT(*) as count FROM records_log WHERE room = ? GROUP BY pc_id");
        $pcallrooms->bind_param("s", $room);
        $pcallrooms->execute();
        $resultPCsAllRooms = $pcallrooms->get_result();

        while ($row = $resultPCsAllRooms->fetch_assoc()) {
            $countpc2[$row['pc_id']] = isset($countpc2[$row['pc_id']]) ? $countpc2[$row['pc_id']] + $row['count'] : $row['count'];
            $totalcountpc2s += $row['count'];
        }
    }
}
?>


<div id ="chart-container" class="container-fluid print-area">
            <!-- Doughnut Chart for Records Log (Room-wise) -->
    <div style="display: flex; flex-wrap: wrap; height: auto; ">
    <!-- First Chart: Room Logs Count -->
    <div class="card card-flush mb-0 mb-xl-10" style="width: 25%; height: 100%; margin-right: 5px;">
        <div class="card-header pt-5">
            <div class="card-title d-flex flex-column">
                <div class="d-flex align-items-center">
                    <span class="fs-2hx fw-bold text-black-900 me-2 lh-1 ls-n2"><?php echo number_format($totalcountpc2s); ?></span>
                    <span class="fs-4 fw-semibold me-1 align-self-start">📝</span>
                   <form id="roomForm" method="get">
    <label for="room" style="font-size: 13px;">Select Room:</label>
    <select style="font-size: 13px;" name="room" id="room">
        <option style="font-size: 13px;" value="">Overall</option>
        <?php foreach ($rooms as $room): ?>
            <option style="font-size: 13px;" value="<?php echo $room; ?>" <?php echo $room == $roomselected2 ? 'selected' : ''; ?>>
                <?php echo $room; ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>
                </div>
                <span class="text-black-500 pt-4 fw-semibold fs-6">Logs Count by Room</span>
            </div>
        </div>
        <div class="card-body pt-1 pb-0 d-flex align-items-center" style="height: 100%;">
            <div class="d-flex flex-center me-0 pt-0" style="width: 100%; height: 100%;">
                <canvas style="width:100%; display: flex; height:200px; position: relative; " id="roomChartLogs"></canvas>
            </div>
        </div>
    </div>

    <!-- Second Chart: PC Logs Count -->
    <div class="card card-flush mb-5 mb-xl-10" style="width: 74.3%; height: 100%;">
        <div class="card-header pt-5">
            <div class="card-title d-flex flex-column">
                <div class="d-flex align-items-center">
                    <span class="fs-2hx fw-bold text-black-900 me-2 lh-1 ls-n2"></span>
                    <span class="fs-4 fw-semibold me-1 align-self-start">
                        <img width="23" height="23" src="workstation.png" alt="workstation"/>
                    </span>
                </div>
                <span class="text-gray-500 pt-4 fw-semibold fs-6" style="color:black !important;"><?php echo !empty($roomselected2) ? $roomselected2 : "Overall"; ?> : PC Logs</span>
            </div>
        </div>
        <div class="card-body pt-3 pb-4 d-flex align-items-center" style="height: 100%;">
            <div class="d-flex flex-center me-5 pt-2" style="width: 100%; height: 100%;">
                <canvas style="width:100%; display: flex; height:200px;   position: relative;" id="barChartLogs"></canvas>
            </div>
        </div>
    </div>



<script src="assets/chart/chart.js"></script>
<script src="assets/chart/chartjs-plugin-datalabels.js"></script>

<script>
$(document).ready(function () {
    $('#room').on('change', function () {
        var selectedRoom = $(this).val();
        $.ajax({
            url: window.location.href,
            type: 'GET',
            data: { room: selectedRoom },
            success: function (response) {
                const newContainer = $(response).find('#chart-container');
                $('#chart-container').html(newContainer.html());
                initializeCharts();
            },
            error: function (xhr, status, error) {
                console.error('Error fetching data:', error);
            }
        });
    });

    // Initialize charts on page load
    initializeCharts();
});

function initializeCharts() {

// Register the datalabels plugin
Chart.register(ChartDataLabels);

// Total counts for Logs
const totallogcountss = <?php echo array_sum($countroomssss); ?>;

// Doughnut Chart for Room Counts (Logs)
const countroomssss = <?php echo json_encode(array_values($countroomssss)); ?>;
const roomLabels = <?php echo json_encode(array_keys($countroomssss)); ?>;

const roomChartLogs = new Chart(document.getElementById('roomChartLogs').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: roomLabels,
        datasets: [{
            label: 'Room Log Counts',
            data: countroomssss,
            backgroundColor: [
                '#4F81BD', '#76B7B2', '#4C9F70', '#032F30',
                '#F1916D', '#19305C', '#AE7DAC', '#C48CB3'
            ],
            borderColor: 'rgba(0, 0, 0, 0.1)',
            borderWidth: 1
        }]
    },
    options: {
         maintainAspectRatio: false,
        responsive: true,
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
                    const percentage = Math.round((value / totallogcountss) * 100);
                    return percentage + '%';
                },
                color: 'black',
                font: {
                    weight: 'plain',
                    size: 12
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});

const countpc2 = <?php echo json_encode(array_values($countpc2)); ?>;
const pcLabels = <?php echo json_encode(array_keys($countpc2)); ?>;
const ctxLogs = document.getElementById('barChartLogs').getContext('2d');
const gradient = ctxLogs.createLinearGradient(0, 0, 400, 0);
gradient.addColorStop(0, '#4F81BD');
gradient.addColorStop(1, '#4C9F70');

const barChartLogs = new Chart(ctxLogs, {
    type: 'bar',
    data: {
        labels: pcLabels,
        datasets: [{
            label: 'PC Log Counts',
            data: countpc2,
            backgroundColor: gradient,
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                labels: {
                    color: 'black'
                }
            },
            datalabels: {
                display: true,
                color: 'black',
                font: {
                    weight: 'bold',
                    size: 12
                },
                anchor: 'center', // Position at the top of the bar
                align: 'center'
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: 'black',
                },
                ticks: {
                    color: 'black',
                }
            },
            y: {
                grid: {
                    color: 'black',
                },
                ticks: {
                    color: 'black',
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});
}
initializeCharts();

</script>
</div>
</div>