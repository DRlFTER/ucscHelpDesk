with open('/Applications/XAMPP/xamppfiles/htdocs/ucscHelpDesk/public/js/ticketFull/ticketFull.js', 'r') as f:
    content = f.read()

content = content.replace('formData.append("ticket_id", getTicketIdFromUrl());\\n          const res = await fetch(`/staff/ticketAssign`', 'formData.append("id", getTicketIdFromUrl());\\n          const res = await fetch(`/staff/ticketAssign`')
content = content.replace('formData.append("ticket_id", getTicketIdFromUrl());\\n          formData.append("staff_id", select.value);', 'formData.append("id", getTicketIdFromUrl());\\n          formData.append("forward_to", select.value);')

with open('/Applications/XAMPP/xamppfiles/htdocs/ucscHelpDesk/public/js/ticketFull/ticketFull.js', 'w') as f:
    f.write(content)
