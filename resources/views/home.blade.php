{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Employee</title>
</head>

<body>
    <h1>Employee Details</h1>

    <div class="cards">
        @foreach ($employees as $employee)
            <div class="card">
                <img src="" alt="">
                <h3>{{ $employee->name }}</h3>
                <p>{{ $employee->department }}</p>
            </div>
        @endforeach

    </div>
</body>

</html> --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Employee Card</title>
    <link rel="stylesheet" href="{{ asset('css/card.css') }}">
</head>

<body>
    @foreach ($employees as $employee)
    <div class="employee-card">
        <div class="card-header">
            <img src="https://via.placeholder.com/100" alt="Employee Photo" class="profile-img">
        </div>
        <div class="card-body">
            <h2 class="employee-name">{{ $employee->name }}</h2>
            <p class="employee-role">{{ $employee->department }}</p>
            <p class="employee-email">{{ $employee->email }}</p>
            <p class="employee-phone">{{ $employee->phone }}</p>
        </div>
        <div class="card-footer">
            <p>Company Name</p>

            <form action=""></form>
        </div>  
    </div>
    @endforeach
</body>

</html>
