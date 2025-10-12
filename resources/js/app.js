import './bootstrap';

// Import Report Components
import ReportViewer from './components/ReportViewer.js';
import DataTable from './components/DataTable.js';
import ExportButtons from './components/ExportButtons.js';

// Make components available globally for Blade templates
window.ReportViewer = ReportViewer;
window.DataTable = DataTable;
window.ExportButtons = ExportButtons;
