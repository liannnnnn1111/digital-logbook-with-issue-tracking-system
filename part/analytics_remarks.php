<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in (secured session)
if (!isset($_SESSION['secured'])) {
    header('Location: login.php');
    exit();
}

// Include the database connection file
include('connect.php');

$roomqueries = "SELECT DISTINCT room FROM records_issue ORDER BY room ASC";
$issueresultroom = $conn->query($roomqueries);
$issueroommm = [];
if ($issueresultroom && $issueresultroom->num_rows > 0) {
    while ($row = $issueresultroom->fetch_assoc()) {
        $issueroommm[] = $row['room'];
    }
}

$selectedissueroomss = isset($_GET['issue_room']) ? $conn->real_escape_string($_GET['issue_room']) : '';

$issueroomcountss = [];
$totalissuecountrooms = 0;
$totalqueryissueroomcountsss = "SELECT room, COUNT(*) as count FROM records_issue GROUP BY room ORDER BY room ASC";
$issueresultissueroomcountsss = $conn->query($totalqueryissueroomcountsss);
if ($issueresultissueroomcountsss && $issueresultissueroomcountsss->num_rows > 0) {
    while ($row = $issueresultissueroomcountsss->fetch_assoc()) {
        $issueroomcountss[$row['room']] = $row['count'];
        $totalissuecountrooms += $row['count'];
    }
}

$issuepccounts = [];
$counttotalissuepc = 0;

if ($selectedissueroomss) {
    $pcqueryissue = $conn->prepare("SELECT pc_id, COUNT(*) as count FROM records_issue WHERE room = ? GROUP BY pc_id ORDER BY pc_id ASC");
    $pcqueryissue->bind_param("s", $selectedissueroomss);
    $pcqueryissue->execute();
    $resultissuePCs = $pcqueryissue->get_result();

    if ($resultissuePCs && $resultissuePCs->num_rows > 0) {
        while ($row = $resultissuePCs->fetch_assoc()) {
            $issuepccounts[$row['pc_id']] = $row['count'];
            $counttotalissuepc += $row['count'];
        }
    } else {
        $issuepccounts = ['No data' => 0];
    }
} else {
    foreach ($issueroomcountss as $room => $count) {
        $allroomsissuequery = $conn->prepare("SELECT pc_id, COUNT(*) as count FROM records_issue WHERE room = ? GROUP BY pc_id");
        $allroomsissuequery->bind_param("s", $room);
        $allroomsissuequery->execute();
        $resultissuePCsAllRooms = $allroomsissuequery->get_result();

        while ($row = $resultissuePCsAllRooms->fetch_assoc()) {
            $issuepccounts[$row['pc_id']] = isset($issuepccounts[$row['pc_id']]) ? $issuepccounts[$row['pc_id']] + $row['count'] : $row['count'];
            $counttotalissuepc += $row['count'];
        }
    }
}

$conn->close();

?>

<style>
    #roomchartissuew, #barchartissuee {
    width: 100% !important;
    height: 200px !important; /* or any height you need */
}

</style>
<div id ="chart-issue-container" class="container-fluid print-area">
             
    <div style="display: flex; flex-wrap: wrap; height: auto; ">
        <div class="card card-flush mb-5 mb-xl-10" style="width: 25%; height: 100%; margin-right: 5px;">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <span class="fs-2hx fw-bold text-black-900 me-2 lh-1 ls-n2"><?php echo number_format($counttotalissuepc); ?></span>
                        <span class="fs-4 fw-semibold me-1 align-self-start">⚠️</span>
                        <form id="roomForm1" method="get">
                            <label for="issue_room" style="font-size: 13px;">Select Room:</label>
                            <select style="font-size: 13px;" name="issue_room" id="issue_room">
                            <option style="font-size: 13px;" value="">Overall</option>
                            <?php foreach ($issueroommm as $issueroomss): ?>
                            <option style="font-size: 13px;" value="<?php echo $issueroomss; ?>" <?php echo $issueroomss == $selectedissueroomss ? 'selected' : ''; ?>>
                                <?php echo $issueroomss; ?>
                            </option>
                            <?php endforeach; ?>
                            </select>
                        </form>
                    </div>
                    <span class="text-gray-500 pt-4 fw-semibold fs-6" style="color:black !important;">Issues Count by Room</span>
                </div>
            </div>
            <div class="card-body pt-1 pb-0 d-flex align-items-center" style="height: 100%;">
                <div class="d-flex flex-center me-0 pt-0" style="width: 100%; height: 100%;">
                    <canvas style="width:100%; display: flex; height:200px;   position: relative;" id="roomchartissuew"></canvas>
                </div>
            </div>
        </div>

        <!-- Fourth Chart: PC Logs with Issues -->
        <div class="card card-flush mb-5 mb-xl-10" style="width: 74.3%; height: 100%;">
            <div class="card-header pt-5">
                <div class="card-title d-flex flex-column">
                    <div class="d-flex align-items-center">
                        <span class="fs-2hx fw-bold text-black-900 me-2 lh-1 ls-n2"></span>
                        <span class="fs-4 fw-semibold me-1 align-self-start">
                            <img width="23" height="23" src="error.png" alt="error"/>
                        </span>
                    </div>
                    <span class="text-gray-500 pt-4 fw-semibold fs-6" style="color:black !important;"><?php echo !empty($selectedissueroomss) ? $selectedissueroomss : "Overall"; ?> : PC Logs with Issue</span>
                </div>
            </div>
            <div class="card-body pt-3 pb-4 d-flex align-items-center" style="height: 100%;">
                <div class="d-flex flex-center me-5 pt-2" style="width: 100%; height: 100%;">
                    <canvas style="width:100%; display: flex; height:200px;  position: relative;" id="barchartissuee"></canvas>
                </div>
            </div>
        </div>

<!-- Chart.js Scripts -->
<script src="assets/chart/chart.js"></script>
<script src="assets/chart/chartjs-plugin-datalabels.js"></script>

<script>
// Register the datalabels plugin

     $(document).ready(function () {
        $('#issue_room').on('change', function () {
            var selectedissueroomss = $(this).val();
            $.ajax({
                url: window.location.href, // Ensure correct PHP file
                type: 'GET',
                data: { issue_room: selectedissueroomss },
                success: function (response) {
                    const newContainer = $(response).find('#chart-issue-container');
                    $('#chart-issue-container').html(newContainer.html());
                    // Ensure charts are initialized after content is loaded
                    initializeCharts(); 
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        // Initialize charts when the page loads (only once, not on every AJAX response)
        if (!window.chartsInitialized) {
            initializeCharts();
            window.chartsInitialized = true; // Set a flag to prevent reinitialization
        }
    });

// Function to initialize the charts
function initializeCharts() {
    // Check if the chart container is available before initializing charts
    if (document.getElementById('roomchartissuew') && document.getElementById('barchartissuee')) {

        // Register the datalabels plugin (if not already registered)
        Chart.register(ChartDataLabels);

        // Get the total issue count
        const totalIssueCount = <?php echo array_sum($issueroomcountss); ?>;

        // Doughnut Chart for Room Counts (Issues)
        const roomCounts = <?php echo json_encode(array_values($issueroomcountss)); ?>;
        const roomLabels = <?php echo json_encode(array_keys($issueroomcountss)); ?>;
        const ctxRoomChart = document.getElementById('roomchartissuew').getContext('2d');
        const roomChart = new Chart(ctxRoomChart, {
            type: 'doughnut',
            data: {
                labels: roomLabels,
                datasets: [{
                    label: 'Room Issue Counts',
                    data: roomCounts,
                    backgroundColor: ['#f6d55c', '#ff6b35', '#2a9d8f', '#FFEB99', '#C0C5CE', '#F0EAD6', '#FFDBAC'],
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
                            label: function (tooltipItem) {
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
                        formatter: function (value) {
                            const percentage = Math.round((value / totalIssueCount) * 100);
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

        // Bar Chart for PC Issue Counts
        const pcCounts = <?php echo json_encode(array_values($issuepccounts)); ?>;
        const pcLabels = <?php echo json_encode(array_keys($issuepccounts)); ?>;
        const ctxBarChart = document.getElementById('barchartissuee').getContext('2d');
        const gradient = ctxBarChart.createLinearGradient(0, 0, 400, 0);
        gradient.addColorStop(0, '#ff6b35');
        gradient.addColorStop(1, '#2a9d8f');

        const barChart = new Chart(ctxBarChart, {
            type: 'bar',
            data: {
                labels: pcLabels,
                datasets: [{
                    label: 'PC Issue Counts',
                    data: pcCounts,
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
}


initializeCharts();

</script>

   </div>
</div>


