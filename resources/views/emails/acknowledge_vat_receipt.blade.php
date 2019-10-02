<p>Dear {{ strtoupper($application->user->focal_point->LAST_NAME) }}, {{ ucwords(strtolower($application->user->focal_point->OTHER_NAMES)) }}, <p>

<p>We are in receipt of your VAT Exemtion Application.</p>
<p>Case No. {{ $application->CASE_NO }}</p>

From,
Host Country