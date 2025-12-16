<p>New admission application received:</p>
<ul>
    <li>Name: {{ $admission->first_name }} {{ $admission->last_name }}</li>
    <li>Email: {{ $admission->email }}</li>
    <li>Mobile: {{ $admission->mobile_prefix }} {{ $admission->mobile_no }}</li>
    <li>Discipline: {{ $admission->discipline }}</li>
    <li>Level: {{ $admission->level }}</li>
    <li>Programme: {{ $admission->programme }}</li>
    <li>Submitted At: {{ $admission->created_at }}</li>
</ul>
