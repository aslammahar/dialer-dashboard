<!-- resources/views/voiceqa/create.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <style>
        form {
            width: 70%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="radio"] {
            margin-right: 5px;
        }

        button {
            padding: 10px 15px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Optional: Style the datalist dropdown */
        input[list] {
            cursor: pointer;
        }

        datalist {
            display: none;
        }

        input[list] + datalist option {
            display: block;
        }
    </style>
</head>
<body>
    <form method="POST" action="{{ route('voiceqa.store') }}">
        @csrf
    
        
    
        <label for="agent_id">Agent Name:</label>
    <select name="agent_id" required>
        @foreach($voiceUsers as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endforeach
    </select>
    
        <label for="lead_id">Lead ID:</label>
        <input type="number" name="lead_id" required>
    
        <label for="dialer_id">Dialer ID:</label>
        <input type="number" name="dialer_id" required>
    
        <label for="verifiers">Verifiers:</label>
        <input type="text" name="verifiers">
    
        <label for="recording">Recording:</label>
        <input type="text" name="recording" required>
    
        <label>Greetings:</label>
    <input type="radio" name="GREETINGS" value="Yes" required> Yes
    <input type="radio" name="GREETINGS" value="No" required> No
    
    <label>PITCH Call About:</label>
    <input type="radio" name="PITCH_Call_About" value="Yes" required> Yes
    <input type="radio" name="PITCH_Call_About" value="No" required> No
    
    <label>Age:</label>
    <input type="radio" name="AGE" value="Yes" required> Yes
    <input type="radio" name="AGE" value="No" required> No
    
    <label>Smoker:</label>
    <input type="radio" name="Smoker" value="Yes" required> Yes
    <input type="radio" name="Smoker" value="No" required> No
    
    <label>Health1:</label>
    <input type="radio" name="Health1" value="Yes" required> Yes
    <input type="radio" name="Health1" value="No" required> No
    
    <label>Beneficiary:</label>
    <input type="radio" name="Beneficiary" value="Yes" required> Yes
    <input type="radio" name="Beneficiary" value="No" required> No
    
    <label>Account:</label>
    <input type="radio" name="Account" value="Yes" required> Yes
    <input type="radio" name="Account" value="No" required> No
    
    <label>Plan:</label>
    <input type="radio" name="Plan" value="Yes" required> Yes
    <input type="radio" name="Plan" value="No" required> No
    
    <label>Transfer Details:</label>
    <input type="radio" name="Transfer_details" value="Yes" required> Yes
    <input type="radio" name="Transfer_details" value="No" required> No
    
    <label>Xfer Consent:</label>
    <input type="radio" name="Xfer_Consent" value="Yes">
    <input type="radio" name="Xfer_Consent" value="No">
    
    <label>Rebuttals:</label>
    <input type="radio" name="Rebuttals" value="Yes" required> Yes
    <input type="radio" name="Rebuttals" value="No" required> No
    
    
        <label for="COMMENTS">Comments:</label>
        <textarea name="COMMENTS" rows="4"></textarea>
    
        <label for="Status">Status:</label>
        <input type="text" name="Status" list="statusOptions" required>
        
        <datalist id="statusOptions">
          <option value="Approved">
          <option value="Rejected">
        </datalist>
        
        
    
        <label for="QA_Person">QA Person:</label>
        <input type="text" name="QA_Person" required>
    
        <label for="Use_of_Rebuttals">Use of Rebuttals:</label>
        <input type="number" name="Use_of_Rebuttals" required>
    
        <label for="No_of_Refusals">No of Refusals:</label>
        <input type="number" name="No_of_Refusals" required>
    
        <label for="count">Count:</label>
        <input type="number" name="count" required>
    
        <button type="submit">Submit</button>
    </form>
    
</body>
</html>






