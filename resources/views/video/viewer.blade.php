<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seizure Video Evidence</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #334155;
        }

        .header h1 {
            color: #e53e3e;
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .header p {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .video-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 0 auto 30px;
            background: #1e293b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        }

        .video-player {
            width: 100%;
            height: auto;
            display: block;
            background: #000;
        }

        .video-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            text-align: center;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .seizure-info {
            background: #1e293b;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid #334155;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            background: #0f172a;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #e53e3e;
        }

        .info-label {
            font-size: 0.8rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #f1f5f9;
        }

        .warning-box {
            background: #7f1d1d;
            border: 1px solid #dc2626;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        .warning-box h3 {
            color: #fecaca;
            margin-bottom: 10px;
            font-size: 1.2rem;
        }

        .warning-box p {
            color: #fca5a5;
            font-size: 0.9rem;
        }

        .footer {
            margin-top: auto;
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #334155;
            color: #64748b;
            font-size: 0.8rem;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 300px;
            color: #94a3b8;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #334155;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error-message {
            background: #7f1d1d;
            border: 1px solid #dc2626;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .error-message h3 {
            color: #fecaca;
            margin-bottom: 10px;
        }

        .error-message p {
            color: #fca5a5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header h1 {
                font-size: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .video-controls {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }

            .seizure-info {
                padding: 20px;
            }

            .info-item {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎥 Seizure Video Evidence</h1>
            <p>Secure medical video documentation</p>
        </div>

        @if($seizure && $videoData)
            @if($emergencyStatus && $emergencyStatus['is_emergency'])
                <div class="warning-box">
                    <h3>⚠️ Medical Emergency Detected</h3>
                    @if($emergencyStatus['status_epilepticus'])
                        <p><strong>Possible Status Epilepticus:</strong> Seizure duration ({{ $seizure->calculated_duration ?? 'Unknown' }} min) exceeds emergency threshold</p>
                    @endif
                    @if($emergencyStatus['cluster_emergency'])
                        <p><strong>Seizure Cluster:</strong> Multiple seizures detected within emergency timeframe</p>
                    @endif
                    <p style="margin-top: 10px; font-size: 0.85rem;">This seizure met emergency criteria. Consult with healthcare provider immediately.</p>
                </div>
            @endif

            <div class="video-container">
                <video class="video-player" controls preload="metadata" poster="">
                    <source src="{{ request()->url() }}" type="{{ $videoData['mime_type'] }}">
                    <p>Your browser doesn't support video playback. <a href="{{ request()->url() }}?download=1" style="color: #3b82f6;">Download the video</a> instead.</p>
                </video>
            </div>

            <div class="video-controls">
                <a href="{{ request()->url() }}?download=1" class="btn btn-success">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM4.5 7.5a.5.5 0 0 1 .5-.5h2V4a.5.5 0 0 1 1 0v3h2a.5.5 0 0 1 0 1H8v3a.5.5 0 0 1-1 0V8H5a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    Download Video
                </a>
                <button onclick="toggleFullscreen()" class="btn btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1h-4zM10 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 16 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5zM.5 10a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 14.5v-4a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                    Fullscreen
                </button>
            </div>

            <div class="seizure-info">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Seizure Date & Time</div>
                        <div class="info-value">{{ $seizure->start_time->format('l, F j, Y \a\t g:i A') }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Duration</div>
                        <div class="info-value">
                            @if($seizure->calculated_duration)
                                {{ $seizure->calculated_duration }} minutes
                            @else
                                Not recorded
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Severity Rating</div>
                        <div class="info-value">
                            @if($seizure->severity)
                                {{ $seizure->severity }}/10
                                @if($seizure->severity <= 3)
                                    <span style="color: #10b981;">(Mild)</span>
                                @elseif($seizure->severity <= 6)
                                    <span style="color: #f59e0b;">(Moderate)</span>
                                @else
                                    <span style="color: #ef4444;">(Severe)</span>
                                @endif
                            @else
                                Not recorded
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Seizure Type</div>
                        <div class="info-value">{{ $seizure->seizure_type ? Str::headline($seizure->seizure_type) : 'Not specified' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Video File Size</div>
                        <div class="info-value">{{ app('App\Services\VideoUploadService')->getVideoSize($seizure) ?? 'Unknown' }} MB</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Access Expires</div>
                        <div class="info-value">{{ $seizure->video_expires_at?->format('M j, Y g:i A') }}</div>
                    </div>
                </div>

                @if($seizure->video_notes)
                    <div style="margin-top: 20px; padding: 15px; background: #0f172a; border-radius: 8px; border-left: 4px solid #3b82f6;">
                        <div class="info-label">Video Notes</div>
                        <div style="color: #f1f5f9; margin-top: 8px; white-space: pre-wrap;">{{ $seizure->video_notes }}</div>
                    </div>
                @endif

                @if($seizure->notes)
                    <div style="margin-top: 15px; padding: 15px; background: #0f172a; border-radius: 8px; border-left: 4px solid #94a3b8;">
                        <div class="info-label">General Notes</div>
                        <div style="color: #f1f5f9; margin-top: 8px; white-space: pre-wrap;">{{ $seizure->notes }}</div>
                    </div>
                @endif
            </div>

        @else
            <div class="error-message">
                <h3>Video Not Found</h3>
                <p>The requested video is not available or access has expired.</p>
            </div>
        @endif

        <div class="footer">
            <p><strong>EpiCare Seizure Tracker</strong> - Medical Video Documentation</p>
            <p style="margin-top: 5px;">This video is for medical purposes only. Keep this link confidential.</p>
            <p style="margin-top: 10px; font-size: 0.75rem;">Generated: {{ now()->format('F j, Y \a\t g:i A T') }}</p>
        </div>
    </div>

    <script>
        // Fullscreen functionality
        function toggleFullscreen() {
            const video = document.querySelector('.video-player');

            if (video.requestFullscreen) {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    video.requestFullscreen();
                }
            } else if (video.webkitRequestFullscreen) {
                if (document.webkitFullscreenElement) {
                    document.webkitExitFullscreen();
                } else {
                    video.webkitRequestFullscreen();
                }
            } else if (video.msRequestFullscreen) {
                if (document.msFullscreenElement) {
                    document.msExitFullscreen();
                } else {
                    video.msRequestFullscreen();
                }
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            const video = document.querySelector('.video-player');

            switch(e.key) {
                case ' ':
                    e.preventDefault();
                    if (video.paused) {
                        video.play();
                    } else {
                        video.pause();
                    }
                    break;
                case 'f':
                case 'F':
                    toggleFullscreen();
                    break;
                case 'ArrowLeft':
                    video.currentTime = Math.max(0, video.currentTime - 10);
                    break;
                case 'ArrowRight':
                    video.currentTime = Math.min(video.duration, video.currentTime + 10);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    video.volume = Math.min(1, video.volume + 0.1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    video.volume = Math.max(0, video.volume - 0.1);
                    break;
            }
        });

        // Video loading error handling
        document.querySelector('.video-player').addEventListener('error', function() {
            const container = document.querySelector('.video-container');
            container.innerHTML = `
                <div style="padding: 40px; text-align: center; background: #7f1d1d; color: #fca5a5;">
                    <h3>Unable to Load Video</h3>
                    <p>There was an error loading the video file.</p>
                    <a href="${window.location.href}?download=1" style="color: #60a5fa; margin-top: 15px; display: inline-block;">Try downloading instead</a>
                </div>
            `;
        });

        // Show loading indicator while video loads
        const video = document.querySelector('.video-player');
        const container = document.querySelector('.video-container');

        video.addEventListener('loadstart', function() {
            // Video is starting to load
        });

        video.addEventListener('canplay', function() {
            // Video is ready to play
        });

        // Prevent right-click on video (basic protection)
        video.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Add play/pause click handler
        video.addEventListener('click', function() {
            if (this.paused) {
                this.play();
            } else {
                this.pause();
            }
        });
    </script>
</body>
</html>
