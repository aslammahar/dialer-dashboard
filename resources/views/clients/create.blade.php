<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .form-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 700px;
            margin: 50px auto;
        }
        .form-title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #343a40;
        }
        .form-label {
            font-weight: 500;
            color: #495057;
        }
        .form-select, .form-control {
            border-radius: 8px;
        }
        .btn-primary {
            background: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease-in-out;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Create Client</h1>
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="clientType" class="form-label">Select Client Type</label>
                <select id="clientType" name="client_type" class="form-select" required>
                    <option value="">-- Select Client Type --</option>
                    <option value="parent">Client</option>
                    <option value="child">Child Client</option>
                </select>
            </div>
            <div id="parentClientSection" class="mb-4" style="display: none;">
                <label for="parentClient" class="form-label">Select Parent Client</label>
                <select id="parentClient" name="parent_id" class="form-select">
                    <option value="">-- Select Parent Client --</option>
                    @foreach($parentClients as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div id="clientDetailsSection" style="display: none;">
                <div class="mb-4">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Client Name" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter Client Email" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter Password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Create Client</button>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function () {
            $('#clientType').change(function () {
                const clientType = $(this).val();
                if (clientType === 'parent') {
                    $('#parentClientSection').hide();
                    $('#clientDetailsSection').show();
                    $('#parentClient').removeAttr('required');
                } else if (clientType === 'child') {
                    $('#parentClientSection').show();
                    $('#clientDetailsSection').show();
                    $('#parentClient').attr('required', 'required');
                } else {
                    $('#parentClientSection').hide();
                    $('#clientDetailsSection').hide();
                }
            });
        });
    </script>
</body>
</html>
