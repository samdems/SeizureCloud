# PDF Export Setup Instructions

This guide will help you set up PDF export functionality for your seizure tracking system.

## Quick Setup

### 1. Install the PDF Package

Open your Laragon Terminal (or command prompt in your project directory) and run:

```bash
composer require barryvdh/laravel-dompdf
```

### 2. Create Font Directory

```bash
mkdir storage/fonts
```

That's it! The PDF export buttons will now work.

## Usage

Once installed, you'll have access to two types of PDF exports:

### Monthly PDF Export

- **Access**: From the seizure index page, click "Export PDF" dropdown
- **Options**: 
  - Current month
  - Previous month  
  - Custom date range (via modal)
- **Content**: Complete monthly report with statistics and all seizures
- **URL**: `/seizures/export/monthly-pdf?month=X&year=Y`

### Single Seizure PDF Export

- **Access**: 
  - From seizure index page (PDF icon button in actions column)
  - From individual seizure detail page ("Export PDF" button)
- **Content**: Detailed report of a specific seizure including:
  - Complete seizure information
  - Active medications at time of seizure
  - Vitals recorded on seizure day
  - Emergency status indicators
  - All notes and additional details

## Features

### Monthly PDF Includes:
- Patient information
- Date range summary
- Key statistics (total seizures, average severity, duration totals)
- Complete seizure table with all recorded details
- Emergency seizures highlighted
- Professional medical formatting

### Single Seizure PDF Includes:
- Complete seizure timeline (start, end, duration)
- Severity rating with color coding
- Emergency status banner (if applicable)
- Triggers and pre-ictal symptoms
- Medication information active during seizure
- Vitals from the seizure day
- All notes (general, recovery, medication, wellbeing)
- Medical contact information
- Professional clinical formatting

## PDF Features

- **Professional Medical Layout**: Clean, clinical appearance suitable for healthcare providers
- **Emergency Indicators**: Clear visual warnings for status epilepticus and emergency situations
- **Color-Coded Severity**: Visual severity indicators (green/yellow/red)
- **Comprehensive Data**: All tracked information included
- **Print-Optimized**: Properly formatted for printing
- **Secure**: Only accessible by authenticated users with proper permissions
- **Responsive**: Works on all devices

## Security

- PDF exports respect user permissions
- Users can only export their own data
- Trusted access users can export for accounts they have access to
- All exports require authentication

## File Naming

- Monthly PDFs: `seizures_{username}_{year}-{month}.pdf`
- Single seizure PDFs: `seizure_{id}_{date}_time}.pdf`

## Troubleshooting

### Common Issues

1. **"Class not found" errors**: Run `composer install` to install dependencies
2. **Font issues**: Ensure `storage/fonts` directory exists and is writable
3. **Memory issues**: For large datasets, consider increasing PHP memory limit
4. **Styling issues**: Check that CSS is properly embedded in the PDF templates

### Performance Tips

- Monthly exports with many seizures may take longer to generate
- Consider pagination for very large datasets
- PDF generation uses server resources - monitor performance

## Customization

### Styling
- Edit templates in `resources/views/seizures/pdf/`
- Modify CSS in the template files
- Colors, fonts, and layout can be customized

### Content
- Add or remove fields by editing the PDF templates
- Modify statistics calculations in the controller
- Add new export types by extending the controller

## Support

The PDF export system integrates seamlessly with:
- Existing seizure tracking
- User permissions and trusted access
- Emergency detection systems
- Medication tracking
- Vitals integration

## Installation Note

The PDF export buttons are already added to your seizure pages, but they'll show an error message until you install the package above. Once installed, they'll work immediately.

For technical issues, check Laravel logs and ensure all dependencies are properly installed.