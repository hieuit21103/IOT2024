<?php
require 'connect.php';
$stmt = $pdo->prepare("SELECT * FROM `auto_conf` WHERE 1");
$stmt->execute();
$results =$stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Control Panel</title>
    <style>
    	.container{
    		margin-top: 200px;
    	}
    	.col-7{
    		margin: 20px;
    	}
    	.col-4{
    		margin: 20px;
    	}
        .progress-circle-back {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#fff 0% 25%, transparent 25% 100%);
            background-color: #4db8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
            margin: 25px;
        }
        .progress-circle {
            position: relative;
            width: 80%;
            height: 80%;
            border-radius: 50%;
            display: flex;
            background-color: white;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
        }

        .progress-circle-text {
            font-size: 24px;
            color: #333;
        }

        .dot{
        	width: 10px;
        	height: 10px;
        	border-radius: 50%;
        	background-color: red;
        	display: inline-block;
            margin: 2px;
        }	
        .status-container {
            display: flex;
        }

    </style>
</head>
<body>
	<div class="modal fade" id="auto-config" tabindex="-1" aria-labelledby="auto-config-label" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" id="auto-config-label">Auto Config</h5>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="document.getElementById('mode-switch').checked = false;"></button>
	            </div>
	            <form id="form-cfg">
		            <div class="modal-body">    
		                    <div class="mb-3">
		                        <label for="dropdown" class="form-label">Preset</label>
		                        <select class="form-select" id="dropdown" name="preset">
		                            <option selected>Create New</option>
		                            <?php foreach ($results as $row) {?>
		                            <?php 
		                            	echo "<option>".$row['name']."</option>";}
		                            ?>
		                        </select>
		                    </div>
		                    <div class="mb-3">
		                    	<label for="presetId" class="form-label">ID</label>
		                    	<input type="number" class="form-control" id="presetId" name="id" value="<?php //echo $row['id'];?>" readonly="true"></input>
		                    </div>
		                    <div class="mb-3">
		                        <label for="inputName" class="form-label">Name</label>
		                        <input type="text" class="form-control" id="inputName" value="<?php //echo $row['name'];?>" name="name" placeholder="Preset Name" required>
		                    </div>
		                    <div class="mb-3">
		                        <label for="inputSpeed" class="form-label">Speed</label>
		                        <input type="number" class="form-control" id="inputSpeed" value="<?php //echo $row['speed'];?>" name="speed" placeholder="1-255" min="1" max="255" required>
		                    </div>
		                    <div class="mb-3">
		                        <label for="textarea" class="form-label">Time</label>
		                        <input type="text" class="form-control" id="inputTime" name="time" rows="3" value="<?php //echo $row['time'];?>" placeholder="Split by a space (hh:mm)" required></textarea>
		                    </div>
		                    <?php //} ?>
		            </div>
		            <div class="modal-footer">
		                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="document.getElementById('mode-switch').checked = false;">Close</button>
		                <button type="submit" class="btn btn-primary" id="submit-config">Submit</button>
		            </div>
	        	</form>
	        </div>
	    </div>
	</div>
	<div class="container" style="background-color: #f5f5dc">
		<div class="row">
			<div class="col-7">
				<h3>Control <p id="time" style="display: inline-block;margin: 2px;float: right;"></p></h3><hr>
				<div class="form-check form-switch">
					<input class="form-check-input" type="checkbox" role="switch" id="pump-switch">
					<label class="form-check-label" for="pump-switch">Pump</label>
				</div>
				<div class="slidecontainer">
						<label for="voltage-range">Motor Speed</label>
						<div class="d-flex align-items-center">
							<div class="col-9" style="margin-right: 10px;">
				            	<input type="range" min="1" max="255" value="127" class="form-range me-3" id="voltage-range">
				            </div>
				            <div class="col-2">
				            	<input class="form-control" type="number" min="1" max="255" value="127" id="voltage-input">
				            </div>
				        </div>
				</div>
				<div class="form-check form-switch">
					<input class="form-check-input" type="checkbox" role="switch" id="mode-switch">
					<label class="form-check-label" for="mode-switch" id="label-mode-switch"></label>
				</div>
				<div class="logs">
					<a href="log.php" class="btn btn-primary" id="logs-btn" style="float:	right;">Logs</a>
				</div>
			</div>
			<div class="col-4">
				<div class="status">
					<h3>Status <p class="dot"></p></h3> 
				</div>
				<hr>
				<div class="status-2" style="display: flex;justify-content: center;">
					<div class="progress-circle-back" style="display: flex;" id="moisture-texture">
				        <div class="progress-circle">
				            <div class="progress-circle-text" id="moisture"></div>
				        </div>
				    </div>
				    <div class="progress-circle-back" style="display: flex; background-color: red" id="temparature-texture">
				        <div class="progress-circle">
				            <div class="progress-circle-text" id="temparature"></div>
				        </div>
				    </div>
			    </div>
			</div>
		</div>
	</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript">
        const url = "http://esp8266.local";
        const voltageRange = document.getElementById('voltage-range');
        const voltageInput = document.getElementById('voltage-input');
        const modeSwitch = document.getElementById('mode-switch');
        const dropdown = document.getElementById('dropdown');
        const presetId = document.getElementById('presetId');
        const inputName = document.getElementById('inputName');
        const inputSpeed = document.getElementById('inputSpeed');
        const inputTime = document.getElementById('inputTime');
        voltageRange.addEventListener('input', function () {
            document.getElementById('voltage-input').value = voltageRange.value;
        });
        voltageInput.addEventListener('input', function(){
        	document.getElementById('voltage-range').value = voltageInput.value;
        	if(voltageInput.value > 255){
        		voltageInput.value = 255;
        	}
        })
        modeSwitch.addEventListener('change',function(){
        	if (modeSwitch.checked) {
        		document.getElementById('label-mode-switch').innerText = "Auto Mode";
        		var myModal = new bootstrap.Modal(document.getElementById('auto-config'), {});
        		myModal.show();
        	}else{
        		document.getElementById('label-mode-switch').innerText = "Manual Mode";
        	}
        })

        const presets = <?php echo json_encode($results); ?>;

        dropdown.addEventListener('change', function() {
            if (dropdown.value === "Create New") {
                presetId.value = "";
                inputName.value = "";
                inputSpeed.value = "";
                inputTime.value = "";
            } else {
                const selectedPreset = presets.find(preset => preset.name == dropdown.value);
                presetId.value = selectedPreset.id;
                inputName.value = selectedPreset.name;
                inputSpeed.value = selectedPreset.speed;
                inputTime.value = selectedPreset.time;
            }
        });


        function updateTime() {
		    const now = new Date();
		    const day = String(now.getDate()).padStart(2, '0');
		    const month = String(now.getMonth() + 1).padStart(2, '0');
		    const year = now.getFullYear();
		    const hours = String(now.getHours()).padStart(2, '0');
		    const minutes = String(now.getMinutes()).padStart(2, '0');
		    const seconds = String(now.getSeconds()).padStart(2, '0');
		    const timeString = `${day}/${month}/${year} - ${hours}:${minutes}:${seconds}`;
		    document.getElementById('time').innerText = timeString;
		}

		setInterval(updateTime, 1000);
		updateTime();

        function getMT() {
            fetch(url+'/json?type=mt')
                .then(response => response.json())
                .then(data => {
                	const moistureValue = data.moisture;
                	const temperatureValue = data.temperature
                    document.getElementById('moisture').innerText = moistureValue
                    document.getElementById('moisture-texture').style.background = `conic-gradient(#fff 0% ${100 - moistureValue}%, transparent 25% 100%)`;
                    document.getElementById('moisture-texture').style.backgroundColor = `#4db8ff`;
                    document.getElementById('temparature').innerText = temperatureValue
                    document.getElementById('temparature-texture').style.background = `conic-gradient(#fff 0% ${100 - temperatureValue}%, transparent 25% 100%)`;
                    document.getElementById('temparature-texture').style.backgroundColor = `red`;
                });
        }

        function getMode() {
        	fetch(url+'/json?type=mode')
                .then(response => response.json())
                .then(data => {
                    if(data.mode == "auto"){
                    	document.getElementById("mode-switch").checked = true;
                    	document.getElementById('label-mode-switch').innerText = "Auto Mode";
                    }else if (data.mode == "manual") {
                    	document.getElementById("mode-switch").checked = false;
                    	document.getElementById('label-mode-switch').innerText = "Manual Mode";
                    }
                    
                });
        }


        function getPStation(){
        	fetch(url+'/json?type=station')
                .then(response => response.json())
                .then(data => {
                    if(data.pump == "on"){
                    	document.getElementById("pump-switch").checked = true;
                    }else if (data.pump == "off") {
                    	document.getElementById("pump-switch").checked = false;
                    }
                });

        }
        getPStation()
        getMode()
        getMT()
        setInterval(getMT, 1000);
        

        document.getElementById('pump-switch').addEventListener('change',function() {
        	if(!this.checked){
        		fetch(url+"/on?speed="+voltageInput.value,{mode:'no-cors'});
        	}else{
        		fetch(url+"/off",{mode:'no-cors'});
        	}
        })

        document.getElementById('mode-switch').addEventListener('change', function() {
            if (!this.checked) {
                fetch(url+"/manual");
            }
        });
        document.getElementById('form-cfg').addEventListener('submit', function(event) {
		    event.preventDefault();
		    fetch('config.php', {
		        method: 'POST',
		        body: new FormData(this)
		    })
		    .then(response => {
		    	if(response.ok){
		    		alert("Auto config success");
		    		fetch(url+"/auto?preset="+presetId.value,{
		    			mode:'no-cors'
		    		})
		    		var modalElement = document.getElementById('auto-config');
				    var modal = bootstrap.Modal.getInstance(modalElement);
				    
				    if (modal) {
				        modal.hide();
				    }
		    	}
		    })
		    .catch(error => console.error('Error:', error));
		});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
</script>
</html>