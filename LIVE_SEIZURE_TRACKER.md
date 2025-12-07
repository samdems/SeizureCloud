# Live Seizure Tracker

A comprehensive real-time seizure tracking system that allows users to track seizures as they happen, with support for trusted access functionality.

## Features

### Core Functionality
- **Real-time Timer**: Live countdown timer that tracks seizure duration in real-time (HH:MM:SS format)
- **User Selection**: Track seizures for yourself or any user you have trusted access to
- **Automatic Form Population**: When timer is stopped, automatically populates seizure form with:
  - Start time (when timer was started)
  - End time (when timer was stopped)
  - Duration in minutes (calculated automatically)

### User Management
- **Trusted Access Support**: View and select from all users you have trusted access to
- **User Avatar Display**: Shows user avatar and information for selected user
- **Permission Validation**: Ensures you can only track seizures for users you have access to

### Timer Controls
- **Start**: Begins tracking seizure time
- **Stop**: Ends tracking and shows seizure form
- **Reset**: Clears timer and form, returns to initial state

### Seizure Form Features
- **Pre-populated Time Data**: Start time, end time, and duration are automatically filled
- **Severity Rating**: Interactive 1-10 scale with visual emoji indicators
- **Additional Information**: Checkboxes for:
  - On period
  - Ambulance called
  - Slept after seizure
- **NHS Contact Type**: Dropdown for GP, Hospital, 111, 999, or None
- **Postictal State**: Optional end time with "Set to now" quick button
- **Notes**: Text area with expandable modal for detailed notes

## Access Points

### Dashboard
- Prominent "Live Tracker" card in the quick stats section
- Real-time emergency tracking emphasis
- Direct link to live tracker

### Seizure Index Page
- "Live Tracker" button in the header
- Timer icon for visual identification

### Direct URL
- `/seizures/live-tracker`

## User Interface

### Layout
- Clean, mobile-responsive design
- Large, easy-to-read timer display
- Intuitive button controls
- Progressive disclosure (form only shows after timer stops)

### Visual Design
- Red/orange gradient for emergency emphasis
- Clear status indicators
- Accessible color schemes
- Professional medical interface

### Interactive Elements
- Hover effects on buttons
- Smooth animations and transitions
- Modal support for expanded note-taking
- Visual feedback for all actions

## Technical Implementation

### Frontend
- **JavaScript**: Vanilla JS for timer functionality and form management
- **Real-time Updates**: Timer updates every second
- **State Management**: Tracks timer state, user selection, and form data
- **Responsive Design**: Works on desktop, tablet, and mobile

### Backend Integration
- **Laravel Routes**: Integrated with existing seizure management system
- **Form Validation**: Full server-side validation with custom request class
- **Database Storage**: Saves to existing seizure records table
- **Trusted Access**: Validates permissions before allowing seizure creation

### Security
- **Authorization Checks**: Validates trusted access permissions
- **Input Validation**: Server-side validation for all form fields
- **CSRF Protection**: Laravel CSRF tokens on all forms

## Data Flow

1. **User Selection**: Choose user from trusted access list
2. **Timer Start**: Record start time, begin real-time counter
3. **Timer Stop**: Record end time, calculate duration
4. **Form Population**: Auto-fill time fields in seizure form
5. **Form Completion**: User fills additional details (severity, notes, etc.)
6. **Data Submission**: Save complete seizure record to database
7. **Redirection**: Return to seizure index with success message

## Validation Rules

### User Access
- Must be authenticated
- Can track for self (always allowed)
- Can track for others only with valid trusted access
- Trusted access must be active and not expired

### Time Data
- Start time is required
- End time must be after start time
- Duration calculated automatically in minutes
- All times stored in user's timezone

### Form Fields
- Severity: Required integer 1-10
- NHS Contact: Optional, must be valid option
- Checkboxes: Boolean values (on_period, ambulance_called, slept_after)
- Notes: Optional text field
- Postictal state end: Optional datetime

## Emergency Integration

### Automatic Emergency Detection
- Integrates with user's emergency thresholds
- Checks for status epilepticus (duration-based emergency)
- Monitors seizure clusters (count-based emergency)
- Visual indicators for emergency situations

### Emergency Settings Integration
- Uses user's configured emergency duration threshold
- Applies user's seizure count and timeframe settings
- Respects user's emergency contact information

## Mobile Considerations

### Touch Interface
- Large, touch-friendly buttons
- Responsive grid layouts
- Optimized for one-handed use during emergencies

### Performance
- Lightweight JavaScript
- Fast loading times
- Offline-capable timer functionality
- Minimal network requests during tracking

## Accessibility

### Screen Readers
- Proper ARIA labels and roles
- Semantic HTML structure
- Descriptive button text and status updates

### Keyboard Navigation
- Full keyboard accessibility
- Logical tab order
- Keyboard shortcuts for critical actions

### Visual Accessibility
- High contrast colors
- Scalable fonts and interface elements
- Clear visual hierarchy

## Future Enhancements

### Potential Features
- Voice-activated controls for hands-free operation
- GPS location tracking during seizures
- Automatic emergency contact notifications
- Integration with wearable devices
- Offline mode with sync capabilities
- Photo/video attachment support

### Technical Improvements
- WebRTC for real-time communication with trusted contacts
- Progressive Web App (PWA) capabilities
- Push notifications for emergency situations
- API endpoints for mobile app integration

## Usage Guidelines

### Best Practices
1. Ensure emergency settings are configured before first use
2. Test timer functionality in non-emergency situations
3. Keep trusted access list updated
4. Regularly review and update emergency contact information
5. Practice using the interface when not in distress

### Emergency Protocol
1. Start timer immediately when seizure begins
2. If possible, note time and circumstances
3. Stop timer when seizure ends
4. Complete form as thoroughly as possible
5. Seek medical attention if emergency thresholds are met

## Support

For technical issues or feature requests, please refer to the main application documentation or contact system administrators.