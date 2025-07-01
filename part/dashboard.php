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

// ----------------- Fetch Data for Room and PC Counts for Logs -----------------
$queryRooms = "SELECT DISTINCT room FROM records_log ORDER BY room ASC";
$resultRooms = $conn->query($queryRooms);
$rooms = [];
if ($resultRooms && $resultRooms->num_rows > 0) {
    while ($row = $resultRooms->fetch_assoc()) {
        $rooms[] = $row['room'];
    }
}

$selectedRoom = isset($_GET['room']) ? $conn->real_escape_string($_GET['room']) : '';

// Query to get room counts for the doughnut chart (all rooms)
$roomCounts = [];
$totalCountRooms = 0;
$queryRoomCounts = "SELECT room, COUNT(*) as count FROM records_log GROUP BY room ORDER BY room ASC";
$resultRoomCounts = $conn->query($queryRoomCounts);
if ($resultRoomCounts && $resultRoomCounts->num_rows > 0) {
    while ($row = $resultRoomCounts->fetch_assoc()) {
        $roomCounts[$row['room']] = $row['count'];
        $totalCountRooms += $row['count'];
    }
}

$pcCounts = [];
$totalCountPCs = 0;

if ($selectedRoom) {
    // Prepared statement to fetch PC counts for the selected room
    $queryPCs = $conn->prepare("SELECT pc_id, COUNT(*) as count FROM records_log WHERE room = ? GROUP BY pc_id ORDER BY pc_id ASC");
    $queryPCs->bind_param("s", $selectedRoom);
    $queryPCs->execute();
    $resultPCs = $queryPCs->get_result();

    if ($resultPCs && $resultPCs->num_rows > 0) {
        while ($row = $resultPCs->fetch_assoc()) {
            $pcCounts[$row['pc_id']] = $row['count'];
            $totalCountPCs += $row['count'];
        }
    } else {
        $pcCounts = ['No data' => 0];
    }
} else {
    foreach ($roomCounts as $room => $count) {
        $queryPCsAllRooms = $conn->prepare("SELECT pc_id, COUNT(*) as count FROM records_log WHERE room = ? GROUP BY pc_id");
        $queryPCsAllRooms->bind_param("s", $room);
        $queryPCsAllRooms->execute();
        $resultPCsAllRooms = $queryPCsAllRooms->get_result();

        while ($row = $resultPCsAllRooms->fetch_assoc()) {
            $pcCounts[$row['pc_id']] = isset($pcCounts[$row['pc_id']]) ? $pcCounts[$row['pc_id']] + $row['count'] : $row['count'];
            $totalCountPCs += $row['count'];
        }
    }
}

// ----------------- Fetch Data for Remarks and PC Counts for Issues -----------------
$roomquery = "SELECT DISTINCT room FROM records_issue ORDER BY room ASC";
$resultissueroom = $conn->query($roomquery);
$roomissue = [];
if ($resultissueroom && $resultissueroom->num_rows > 0) {
    while ($row = $resultissueroom->fetch_assoc()) {
        $roomissue[] = $row['room'];
    }
}

$selectedissueroom = isset($_GET['issue_room']) ? $conn->real_escape_string($_GET['issue_room']) : '';

$roomcount = [];
$totalissuecountrooms = 0;
$totalqueryroomcounts = "SELECT room, COUNT(*) as count1 FROM records_issue GROUP BY room ORDER BY room ASC";
$resultissueroomcounts = $conn->query($totalqueryroomcounts);
if ($resultissueroomcounts && $resultissueroomcounts->num_rows > 0) {
    while ($row = $resultissueroomcounts->fetch_assoc()) {
        $roomcount[$row['room']] = $row['count1'];
        $totalissuecountrooms += $row['count1'];
    }
}

$issuepccounts = [];
$totalissuecountpcs = 0;

if ($selectedissueroom) {
    $queryissuePCs = $conn->prepare("SELECT pc_id, COUNT(*) as count1 FROM records_issue WHERE room = ? GROUP BY pc_id ORDER BY pc_id ASC");
    $queryissuePCs->bind_param("s", $selectedissueroom);
    $queryissuePCs->execute();
    $resultissuePCs = $queryissuePCs->get_result();

    if ($resultissuePCs && $resultissuePCs->num_rows > 0) {
        while ($row = $resultissuePCs->fetch_assoc()) {
            $issuepccounts[$row['pc_id']] = $row['count1'];
            $totalissuecountpcs += $row['count1'];
        }
    } else {
        $issuepccounts = ['No data' => 0];
    }
} else {
    foreach ($roomcount as $room => $count1) {
        $issuequeryPCsAllRooms = $conn->prepare("SELECT pc_id, COUNT(*) as count1 FROM records_issue WHERE room = ? GROUP BY pc_id");
        $issuequeryPCsAllRooms->bind_param("s", $room);
        $issuequeryPCsAllRooms->execute();
        $resultissuePCsAllRooms = $issuequeryPCsAllRooms->get_result();

        while ($row = $resultissuePCsAllRooms->fetch_assoc()) {
            $issuepccounts[$row['pc_id']] = isset($issuepccounts[$row['pc_id']]) ? $issuepccounts[$row['pc_id']] + $row['count1'] : $row['count1'];
            $totalissuecountpcs += $row['count1'];
        }
    }
}

// Close the database connection
$conn->close();
?>


    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
         .card {
    display: flex;
    flex-direction: column;
    width: 25%;
    height: 300px;
    margin-right: 5px;
     padding: 0px !important;
}

.card-body {
    flex-grow: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 0px !important;

}

canvas {
    width: 100%;
    height: 100%;
}

            .no-print {
                display: none;
            }
        }

        .button {
            padding: 5px 20px;
            color: white;
            background-color: #006735;
            border: solid;
            border-radius: 7px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
        }

        h1 {
            text-align: center;
            margin: 5px;
            padding: 3px;
            font-family: 'Arial', sans-serif;
            font-size: 1.7em;
            color: #e0f7e0;
            background-color: #006735;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .card {
            background-color: #E5F6DF;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
           
        }

        .card-header, .card-title {
            color: black !important;
        }

        .card-body {
            height: 100%;
        }
    </style>


     <div id="dashboard">
    <div class="container-fluid print-area">
    <button class="button no-print" onclick="printPage()">Print This Page</button>
    
            <!-- Doughnut Chart for Records Log (Room-wise) -->
            <div style="display: flex; flex-wrap: wrap; height: auto; ">
    <!-- First Chart: Room Logs Count -->
    <div class="card card-flush mb-0 mb-xl-10" style="width: 25%; height: 100%; margin-right: 5px;">
        <div class="card-header pt-5">
            <div class="card-title d-flex flex-column">
                <div class="d-flex align-items-center">
                    <span class="fs-2hx fw-bold text-black-900 me-2 lh-1 ls-n2"><?php echo number_format($totalCountPCs); ?></span>
                    <span class="fs-4 fw-semibold me-1 align-self-start">📝</span>
                   <form method="get">
    <label for="room" style="font-size: 13px;">Select Room:</label>
    <select style="font-size: 13px;" name="room" id="room" onchange="this.form.submit()">
        <option style="font-size: 13px;" value="">Overall</option>
        <?php foreach ($rooms as $room): ?>
            <option style="font-size: 13px;" value="<?php echo $room; ?>" <?php echo $room == $selectedRoom ? 'selected' : ''; ?>>
                <?php echo $room; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <!-- Preserve the selectedissueroom in the URL -->
    <input type="hidden" name="issue_room" value="<?php echo htmlspecialchars($selectedissueroom); ?>">
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
                <span class="text-gray-500 pt-4 fw-semibold fs-6" style="color:black !important;"><?php echo !empty($selectedRoom) ? $selectedRoom : "Overall"; ?> : PC Logs</span>
            </div>
        </div>
        <div class="card-body pt-3 pb-4 d-flex align-items-center" style="height: 100%;">
            <div class="d-flex flex-center me-5 pt-2" style="width: 100%; height: 100%;">
                <canvas style="width:100%; display: flex; height:200px;   position: relative;" id="barChartLogs"></canvas>
            </div>
        </div>
    </div>

    <!-- Third Chart: Issues Count by Room -->
    <div class="card card-flush mb-5 mb-xl-10" style="width: 25%; height: 100%; margin-right: 5px;">
        <div class="card-header pt-5">
            <div class="card-title d-flex flex-column">
                <div class="d-flex align-items-center">
                    <span class="fs-2hx fw-bold text-black-900 me-2 lh-1 ls-n2"><?php echo number_format($totalissuecountpcs); ?></span>
                    <span class="fs-4 fw-semibold me-1 align-self-start">⚠️</span>
                    <form method="get">
    <label for="issue_room" style="font-size: 13px;">Select Room:</label>
    <select style="font-size: 13px;" name="issue_room" id="issue_room" onchange="this.form.submit()">
        <option style="font-size: 13px;" value="">Overall</option>
        <?php foreach ($roomissue as $issueRoom): ?>
            <option style="font-size: 13px;" value="<?php echo $issueRoom; ?>" <?php echo $issueRoom == $selectedissueroom ? 'selected' : ''; ?>>
                <?php echo $issueRoom; ?>
            </option>
        <?php endforeach; ?>
    </select>
    <!-- Preserve the selectedRoom in the URL -->
    <input type="hidden" name="room" value="<?php echo htmlspecialchars($selectedRoom); ?>">
</form>
                </div>
                <span class="text-gray-500 pt-4 fw-semibold fs-6" style="color:black !important;">Issues Count by Room</span>
            </div>
        </div>
        <div class="card-body pt-1 pb-0 d-flex align-items-center" style="height: 100%;">
            <div class="d-flex flex-center me-0 pt-0" style="width: 100%; height: 100%;">
                <canvas style="width:100%; display: flex; height:200px;   position: relative;" id="roomChartIssues"></canvas>
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
                <span class="text-gray-500 pt-4 fw-semibold fs-6" style="color:black !important;"><?php echo !empty($selectedissueroom) ? $selectedissueroom : "Overall"; ?> : PC Logs with Issue</span>
            </div>
        </div>
        <div class="card-body pt-3 pb-4 d-flex align-items-center" style="height: 100%;">
            <div class="d-flex flex-center me-5 pt-2" style="width: 100%; height: 100%;">
                <canvas style="width:100%; display: flex; height:200px;  position: relative;" id="barChartIssues"></canvas>
            </div>
        </div>
    </div>
</div>

</div>
</div>


    



<!-- Chart.js Scripts -->
<script src="assets/chart/chart.js"></script>
<script src="assets/chart/chartjs-plugin-datalabels.js"></script>

<script>
// Register the datalabels plugin
Chart.register(ChartDataLabels);

// Total counts for Logs
const totalLogCount = <?php echo array_sum($roomCounts); ?>;

// Doughnut Chart for Room Counts (Logs)
const roomCounts = <?php echo json_encode(array_values($roomCounts)); ?>;
const roomLabels = <?php echo json_encode(array_keys($roomCounts)); ?>;

const roomChartLogs = new Chart(document.getElementById('roomChartLogs').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: roomLabels,
        datasets: [{
            label: 'Room Log Counts',
            data: roomCounts,
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
                    const percentage = Math.round((value / totalLogCount) * 100);
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

// Total counts for Issues
const totalIssueCount = <?php echo array_sum($roomcount); ?>;

// Doughnut Chart for Room Counts (Issues)
const roomCountsIssues = <?php echo json_encode(array_values($roomcount)); ?>;
const roomLabelsIssues = <?php echo json_encode(array_keys($roomcount)); ?>;

const roomChartIssues = new Chart(document.getElementById('roomChartIssues').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: roomLabelsIssues,
        datasets: [{
            label: 'Room Issue Counts',
            data: roomCountsIssues,
            backgroundColor: [
                '#f6d55c', '#ff6b35', '#2a9d8f', '#FFEB99',
                '#C0C5CE', '#F0EAD6', '#FFDBAC'
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






// First chart (barChartLogs)
// First chart (barChartLogs)
const pcCounts = <?php echo json_encode(array_values($pcCounts)); ?>;
const pcLabels = <?php echo json_encode(array_keys($pcCounts)); ?>;
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

// Second chart (barChartIssues)
const issuePcCounts = <?php echo json_encode(array_values($issuepccounts)); ?>;
const issuePcLabels = <?php echo json_encode(array_keys($issuepccounts)); ?>;
const ctxIssues = document.getElementById('barChartIssues').getContext('2d');
const gradient1 = ctxIssues.createLinearGradient(0, 0, 400, 0);
gradient1.addColorStop(0, '#ff6b35');
gradient1.addColorStop(1, '#2a9d8f');

const barChartIssues = new Chart(ctxIssues, {
    type: 'bar',
    data: {
        labels: issuePcLabels,
        datasets: [{
            label: 'PC Issue Counts',
            data: issuePcCounts,
            backgroundColor: gradient1,
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


 function printPage() {
        window.print();
    }




</script>
