<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Score Etoefl</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        .header {
            padding: 15px;
            text-align: center;
        }

        .header img {
            width: 200px;
            height: auto;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            text-align: center;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 1.2em;
            color: #444444;
        }

        .section-content {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }

        .section-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .section-item-label {
            font-weight: bold;
            color: #555555;
        }

        .section-item-value {
            color: #333333;
        }

        .summary-statistics {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            color: #003366;
        }

        .summary-statistics-error {
            background-color: #ffcccc;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            color: #cc0000;
        }

        .summary-statistics p {
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('storage/nested_question/icon.png') }}" alt="PENTOL Logo">
            <h1>Score Toefl</h1>
        </div>

        <div class="section">
            <div class="section-title">General Information</div>
            <div class="section-content">
                <div class="section-item">
                    <span class="section-item-label">Packet Name:</span>
                    <span class="section-item-value">{{ $initPacketName }}</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">Accuracy Percentage:</span>
                    <span class="section-item-value">{{ $akurasiProsentase }}%</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">TOEFL Score:</span>
                    <span class="section-item-value">{{ $scoreToefl }}</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">TOEFL Level:</span>
                    <span class="section-item-value">{{ $userScoreInformation }}</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">Your Target:</span>
                    <span class="section-item-value">{{ $targetUser }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Listening Section</div>
            <div class="section-content">
                <div class="section-item">
                    <span class="section-item-label">Correct Questions:</span>
                    <span class="section-item-value">{{ $correctQuestionListeningAll }} / {{ $totalQuestionListeningAll
                        }}</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">Part A Accuracy:</span>
                    <span class="section-item-value">{{ $accuracyListeningPartA }}%</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">Part B Accuracy:</span>
                    <span class="section-item-value">{{ $accuracyListeningPartB }}%</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">Part C Accuracy:</span>
                    <span class="section-item-value">{{ $accuracyListeningPartC }}%</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Structure Section</div>
            <div class="section-content">
                <div class="section-item">
                    <span class="section-item-label">Correct Questions:</span>
                    <span class="section-item-value">{{ $correctQuestionStructureAll }} / {{ $totalQuestionStructureAll
                        }}</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">Part A Accuracy:</span>
                    <span class="section-item-value">{{ $accuracyStructurePartA }}%</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">Part B Accuracy:</span>
                    <span class="section-item-value">{{ $accuracyStructurePartB }}%</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Reading Section</div>
            <div class="section-content">
                <div class="section-item">
                    <span class="section-item-label">Correct Questions:</span>
                    <span class="section-item-value">{{ $correctQuestionReading }} / {{ $totalQuestionReading }}</span>
                </div>
                <div class="section-item">
                    <span class="section-item-label">Accuracy:</span>
                    <span class="section-item-value">{{ $accuracyReading }}%</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Summary Statistics</div>
            @if($scoreToefl <= $targetUser) <div class="summary-statistics-error">
                <p>Your score is below your target by {{ $targetUser - $scoreToefl }}
                    points. You can take the test again and improve your score.</p>
        </div>
        @else
        <div class="summary-statistics">
            <p>Great job! You have achieved your target score.</p>
        </div>
        @endif
    </div>
</body>

</html>