import re

with open('app/models/counselor/Reports.php', 'r') as f:
    code = f.read()

# getAllTicketsReport
code = re.sub(
    r'(public function getAllTicketsReport.*?WHERE )t\.division = \?"(.*?)\[\(int\)\$divisions\[0\]\[\'did\'\]\]',
    r'\1t.assigned_to = ?"\2[(int)$counselor_id]',
    code,
    flags=re.DOTALL
)

# getAllTicketsSummary
code = re.sub(
    r'(public function getAllTicketsSummary.*?WHERE )t\.division = \?"(.*?)\[\(int\)\$divisions\[0\]\[\'did\'\]\]',
    r'\1t.assigned_to = ?"\2[(int)$counselor_id]',
    code,
    flags=re.DOTALL
)

# getOverdueTicketsReport
code = re.sub(
    r'(public function getOverdueTicketsReport.*?AND )t\.division = \?"(.*?)\[\(int\)\$divisions\[0\]\[\'did\'\]\]',
    r'\1t.assigned_to = ?"\2[(int)$counselor_id]',
    code,
    flags=re.DOTALL
)

# getOverdueTicketsSummary (Part 1 - main summary)
code = re.sub(
    r'(public function getOverdueTicketsSummary.*?AND )t\.division = \?(.*?)bind_param\(\'i\', \$did\)',
    r'\1t.assigned_to = ?\2bind_param(\'i\', $counselor_id)',
    code,
    flags=re.DOTALL
)

# getOverdueTicketsSummary (Part 2 - days breakdown)
code = re.sub(
    r'(// === NEW: Days Overdue Breakdown ===.*?AND )t\.division = \?(.*?)bind_param\(\'i\', \$did\)',
    r'\1t.assigned_to = ?\2bind_param(\'i\', $counselor_id)',
    code,
    flags=re.DOTALL
)

# getCounselorAssignmentReport
code = re.sub(
    r'(public function getCounselorAssignmentReport.*?WHERE )u\.role = \'counselor\' \s*AND t\.division IN \(" \. implode\(\',\', array_fill\(0, count\(\$did_list\), \'\?\'\)\) \. "\)";(.*?)\$params = \$did_list;(.*?)\$types = str_repeat\(\'i\', count\(\$did_list\)\);',
    r'\1u.u_id = ?";\2$params = [$counselor_id];\3$types = "i";',
    code,
    flags=re.DOTALL
)

# getCounselorAssignmentSummary
code = re.sub(
    r'(public function getCounselorAssignmentSummary.*?WHERE )u\.role = \'counselor\' \s*AND t\.division IN \(" \. implode\(\',\', array_fill\(0, count\(\$did_list\), \'\?\'\)\) \. "\)";(.*?)\$params = \$did_list;(.*?)\$types = str_repeat\(\'i\', count\(\$did_list\)\);',
    r'\1u.u_id = ?";\2$params = [$counselor_id];\3$types = "i";',
    code,
    flags=re.DOTALL
)

# getEscalationReport
code = re.sub(
    r'(public function getEscalationReport.*?WHERE )1=1 AND t\.division IN \(" \. implode\(\',\', array_map\([^)]*\)\) \. "\)";(.*?)\[\];',
    r'\1t.assigned_to = ?";\2[$counselor_id];',
    code,
    flags=re.DOTALL
)
# Note: we need to also update $types = 'i'; for escalation
code = re.sub(
    r'(public function getEscalationReport(?:(?!public function).)*?t\.assigned_to = \?";\s*\$params = \[\$counselor_id\];\s*\$types = )\'\';',
    r'\1\'i\';',
    code,
    flags=re.DOTALL
)


# getEscalationSummary
code = re.sub(
    r'(public function getEscalationSummary.*?WHERE )1=1";(.*?)\[\];',
    r'\1t.assigned_to = ?";\2[$counselor_id];',
    code,
    flags=re.DOTALL
)
code = re.sub(
    r'(public function getEscalationSummary(?:(?!public function).)*?t\.assigned_to = \?";\s*\$params = \[\$counselor_id\];\s*\$types = )\'\';',
    r'\1\'i\';',
    code,
    flags=re.DOTALL
)


# getTicketVolumeTrendsReport
code = re.sub(
    r'(public function getTicketVolumeTrendsReport(?:(?!public function).)*?WHERE )t\.division IN \(\$div_in\)\s*";\s*\$params = \$allowed_dids;\s*\$types = str_repeat\(\'i\', count\(\$allowed_dids\)\);',
    r'\1t.assigned_to = ?";\n    $params = [$counselor_id];\n    $types = "i";',
    code,
    flags=re.DOTALL
)


with open('app/models/counselor/Reports.php', 'w') as f:
    f.write(code)

print("Patched reports")
