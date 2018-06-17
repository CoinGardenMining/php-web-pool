var randomScalingFactor = function(){ return Math.round(Math.random()*1000)};

	var lineChartData = {
		labels : ["January","February","March","April","May","June","July"],
		datasets : [
            {
                label: "Miners Online",
                fillColor : "rgba(220,220,220,0.2)",
                strokeColor : "rgba(220,220,220,1)",
                pointColor : "rgba(220,220,220,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(220,220,220,1)",
                data : [15,11,13,8,11,12,15,9]
            },
			{
				label: "Pool Hashrate",
                fillColor : "rgba(48, 164, 255, 0.2)",
                strokeColor : "rgba(48, 164, 255, 1)",
                pointColor : "rgba(48, 164, 255, 1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(48, 164, 255, 1)",
				data : [9.8,8.5,9.1,5,8.5,9,9.5,9.53]
			}
		]

	}

