<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test</title>
    <style>
        th,td{
            padding: 5px;
        }
    </style>
</head>
<body>
    <h2>List</h2>
    <table class="table" border="1">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">First Name</th>
            <th scope="col">Last Name</th>
            <th scope="col">Email</th>
            <th scope="col">Page</th>
          </tr>
        </thead>
        <tbody>
            @forelse ($list as $key => $contact)
                <tr>
                    <th scope="row">{{ $key + 1 }}</th>
                    <td>{{ $contact->first_name }}</td>
                    <td>{{ $contact->last_name }}</td>
                    <td>{{ $contact->email_address }}</td>
                    <td>{{ $contact->details->page ?? '-' }}</td>
                </tr>
            @empty

            @endforelse
        </tbody>
      </table>
</body>
</html>
