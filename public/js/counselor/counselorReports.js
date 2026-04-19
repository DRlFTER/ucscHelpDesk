/**
 * Counselor Reports - Staff Style
 * Matches the staff reports functionality
 */

const CounselorReports = {
    charts: {},
    currentData: null,

    init() {
        this.setDefaultDates();
        this.setupEventListeners();
        
        // Generate the report immediately on page load
        this.generateReport();
        
        console.log('Counselor Reports initialized');
    },

    setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        document.getElementById('start-date').value = this.formatDate(firstDay);
        document.getElementById('end-date').value = this.formatDate(today);
    },

    setupEventListeners() {
        const generateBtn = document.getElementById('generate-report-btn');
        const exportBtn = document.getElementById('export-csv-btn');

        if (generateBtn) {
            generateBtn.addEventListener('click', () => this.generateReport());
        }

        if (exportBtn) {
            exportBtn.addEventListener('click', () => this.exportPDF());
        }

        // Add hover effect to generate button
        if (generateBtn) {
            generateBtn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 12px rgba(139, 92, 246, 0.4)';
            });
            generateBtn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        }
    },

    async generateReport() {
        const reportType = document.getElementById('report-type').value;
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        const status = document.getElementById('status-filter').value;
        const priority = document.getElementById('priority-filter').value;

        if (!startDate || !endDate) {
            alert('Please select both start and end dates');
            return;
        }

        if (new Date(startDate) > new Date(endDate)) {
            alert('Start date cannot be after end date');
            return;
        }

        try {
            // Show loading state
            this.showLoading();

            // Fetch data
            const data = await this.fetchReportData(reportType, startDate, endDate, status, priority);
            
            this.currentData = data;

            // Update all UI components
            this.updateStatistics(data);
            this.renderPieChart(data);
            this.updateTicketsTable(data.tickets || []);

        } catch (error) {
            console.error('Error generating report:', error);
            alert('Failed to generate report. Please check console for details.');
        }
    },

    async fetchReportData(reportType, startDate, endDate, status, priority) {
        const params = new URLSearchParams({
            report_type: reportType,
            start_date: startDate + ' 00:00:00',
            end_date: endDate + ' 23:59:59'
        });

        if (status && status !== 'all') params.append('status', status);
        if (priority && priority !== 'all') params.append('priority', priority);

        const url = `/counselor/generateReport?${params}`;
        console.log('Fetching report from:', url);

        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        
        if (!result.success) {
            throw new Error(result.error || 'Unknown error occurred');
        }

        return result.data;
    },

    showLoading() {
        document.getElementById('stat-total').textContent = '...';
        document.getElementById('stat-pending-percent').textContent = '...';
        document.getElementById('stat-resolved-percent').textContent = '...';
        document.getElementById('tickets-table-body').innerHTML = 
            '<tr><td colspan="7" style="padding: 24px; text-align: center; color: #6b7280;">Loading...</td></tr>';
    },

    updateStatistics(data) {
        const total = data.total || 0;
        const pending = data.pending || 0;
        const resolved = data.resolved || 0;

        document.getElementById('stat-total').textContent = total;
        
        const pendingPercent = total > 0 ? ((pending / total) * 100).toFixed(1) : '0.0';
        const resolvedPercent = total > 0 ? ((resolved / total) * 100).toFixed(1) : '0.0';
        
        document.getElementById('stat-pending-percent').textContent = pendingPercent + '%';
        document.getElementById('stat-resolved-percent').textContent = resolvedPercent + '%';
        
        document.getElementById('result-count').textContent = (data.tickets || []).length;
    },

    renderPieChart(data) {
        const canvas = document.getElementById('statusPieChart');
        if (!canvas) return;

        // Destroy existing chart
        if (this.charts.statusPie) {
            this.charts.statusPie.destroy();
        }

        const pending = data.pending || 0;
        const assigned = data.assigned || 0;
        const resolved = data.resolved || 0;
        const closed = data.closed || 0;
        const total = data.total || 0;

        // Calculate percentages
        const pendingPercent = total > 0 ? ((pending / total) * 100).toFixed(1) : 0;
        const assignedPercent = total > 0 ? ((assigned / total) * 100).toFixed(1) : 0;
        const resolvedPercent = total > 0 ? ((resolved / total) * 100).toFixed(1) : 0;
        const closedPercent = total > 0 ? ((closed / total) * 100).toFixed(1) : 0;

        const ctx = canvas.getContext('2d');
        this.charts.statusPie = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    `Pending (${pendingPercent}%)`,
                    `Resolved (${resolvedPercent}%)`,
                    `Other (${(parseFloat(assignedPercent) + parseFloat(closedPercent)).toFixed(1)}%)`
                ],
                datasets: [{
                    data: [pending, resolved, assigned + closed],
                    backgroundColor: [
                        '#fbbf24',  // Yellow for pending
                        '#34d399',  // Green for resolved
                        '#60a5fa'   // Blue for others
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            font: {
                                size: 13
                            }
                        }
                    }
                }
            }
        });
    },

    updateTicketsTable(tickets) {
        const tbody = document.getElementById('tickets-table-body');
        if (!tbody) return;

        if (!tickets || tickets.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" style="padding: 24px; text-align: center; color: #6b7280;">No tickets found for the selected filters</td></tr>';
            return;
        }

        tbody.innerHTML = tickets.map(ticket => `
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px; font-size: 14px;">${ticket.ticket_id}</td>
                <td style="padding: 12px; font-size: 14px;">${this.escapeHtml(ticket.title)}</td>
                <td style="padding: 12px;">
                    <span style="${this.getStatusStyle(ticket.status)}">${ticket.status}</span>
                </td>
                <td style="padding: 12px;">
                    <span style="${this.getPriorityStyle(ticket.priority)}">${ticket.priority}</span>
                </td>
                <td style="padding: 12px; font-size: 14px;">${this.escapeHtml(ticket.student_name || 'N/A')}</td>
                <td style="padding: 12px; font-size: 14px;">${this.escapeHtml(ticket.division || 'Counselling')}</td>
                <td style="padding: 12px; font-size: 14px;">${this.formatDateDisplay(ticket.created_at)}</td>
            </tr>
        `).join('');
    },

    getStatusStyle(status) {
        const styles = {
            'pending': 'background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;',
            'agent assigned': 'background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;',
            'resolved': 'background: #d1fae5; color: #065f46; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;',
            'agent-closed': 'background: #e9d5ff; color: #6b21a8; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;',
            'closed': 'background: #e9d5ff; color: #6b21a8; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;'
        };
        return styles[status.toLowerCase()] || styles['pending'];
    },

    getPriorityStyle(priority) {
        const styles = {
            'high': 'background: #fecaca; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;',
            'medium': 'background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;',
            'low': 'background: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;'
        };
        return styles[priority.toLowerCase()] || styles['medium'];
    },

    exportCSV() {
        this.exportPDF();
    },
    
    exportPDF() {
        if (!this.currentData || !this.currentData.tickets || this.currentData.tickets.length === 0) {
            alert('No data to export. Please generate a report first.');
            return;
        }

        if (!window.jspdf || !window.jspdf.jsPDF || typeof window.jspdf.jsPDF !== 'function') {
            alert('PDF generator failed to load. Please refresh and try again.');
            return;
        }

        if (typeof window.jspdf.jsPDF.API.autoTable !== 'function') {
            alert('PDF table generator failed to load. Please refresh and try again.');
            return;
        }

        const reportTypeSelect = document.getElementById('report-type');
        const reportTypeText = reportTypeSelect ? reportTypeSelect.options[reportTypeSelect.selectedIndex].text : 'All Tickets';
        const startDate = document.getElementById('start-date').value;
        const endDate = document.getElementById('end-date').value;
        const status = document.getElementById('status-filter').value;
        const priority = document.getElementById('priority-filter').value;

        const statusLabel = status && status !== 'all' ? status : 'All';
        const priorityLabel = priority && priority !== 'all' ? priority : 'All';

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });

        doc.setFontSize(16);
        doc.text('Counselor Report', 40, 40);

        doc.setFontSize(10);
        doc.text(`Generated: ${new Date().toLocaleString()}`, 40, 58);
        doc.text(`Report Type: ${reportTypeText}`, 40, 74);
        doc.text(`Date Range: ${startDate || 'N/A'} to ${endDate || 'N/A'}`, 40, 88);
        doc.text(`Status: ${statusLabel}`, 40, 102);
        doc.text(`Priority: ${priorityLabel}`, 200, 102);

        doc.setFontSize(11);
        doc.text(`Total: ${this.currentData.total || 0}`, 40, 122);
        doc.text(`Pending: ${this.currentData.pending || 0}`, 130, 122);
        doc.text(`Assigned: ${this.currentData.assigned || 0}`, 230, 122);
        doc.text(`Resolved: ${this.currentData.resolved || 0}`, 340, 122);
        doc.text(`Closed: ${this.currentData.closed || 0}`, 450, 122);

        const tableRows = this.currentData.tickets.map(ticket => [
            ticket.ticket_id || '',
            ticket.title || '',
            ticket.status || '',
            ticket.priority || '',
            ticket.student_name || 'N/A',
            ticket.division || 'Counselling',
            this.formatDateDisplay(ticket.created_at)
        ]);

        doc.autoTable({
            startY: 138,
            head: [['ID', 'Title', 'Status', 'Priority', 'Student', 'Division', 'Date']],
            body: tableRows,
            styles: {
                fontSize: 9,
                cellPadding: 5,
                overflow: 'linebreak'
            },
            headStyles: {
                fillColor: [76, 29, 149],
                textColor: [255, 255, 255],
                fontStyle: 'bold'
            },
            columnStyles: {
                0: { cellWidth: 48 },
                1: { cellWidth: 260 },
                2: { cellWidth: 90 },
                3: { cellWidth: 70 },
                4: { cellWidth: 120 },
                5: { cellWidth: 90 },
                6: { cellWidth: 70 }
            },
            margin: { left: 30, right: 30 }
        });

        const filenameStart = (startDate || 'start').replace(/[^0-9-]/g, '');
        const filenameEnd = (endDate || 'end').replace(/[^0-9-]/g, '');
        doc.save(`counselor_report_${filenameStart}_to_${filenameEnd}.pdf`);
    },

    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    },

    formatDateDisplay(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit' 
        });
    },

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    CounselorReports.init();
});