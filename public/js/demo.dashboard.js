(function ($) {
  "use strict";

  function Dashboard() {
    this.$body = $("body");
    this.charts = [];
  }

  Dashboard.prototype.initCharts = function () {
    window.Apex = {
      chart: {
        parentHeightOffset: 0,
        toolbar: { show: false }
      },
      grid: {
        padding: { left: 0, right: 0 }
      },
      colors: ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"]
    };

    var defaultColors = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"];

    // Line Chart (#revenue-chart)
    var revenueColors = $("#revenue-chart").data("colors");
    var revenueOptions = {
      chart: {
        height: 370,
        type: "line",
        dropShadow: {
          enabled: true,
          opacity: 0.2,
          blur: 7,
          left: -7,
          top: 7
        }
      },
      dataLabels: { enabled: false },
      stroke: {
        curve: "smooth",
        width: 4
      },
      series: [
        { name: "Current Week", data: [10, 20, 15, 25, 20, 30, 20] },
        { name: "Previous Week", data: [0, 15, 10, 30, 15, 35, 25] }
      ],
      colors: revenueColors ? revenueColors.split(",") : defaultColors,
      zoom: { enabled: false },
      legend: { show: false },
      xaxis: {
        type: "string",
        categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        tooltip: { enabled: false },
        axisBorder: { show: false }
      },
      grid: { strokeDashArray: 7 },
      yaxis: {
        stepSize: 9,
        labels: {
          formatter: function (value) {
            return value + "k";
          },
          offsetX: -15
        }
      }
    };
    new ApexCharts(document.querySelector("#revenue-chart"), revenueOptions).render();

    fetch('/api/chart-data')
    .then(response => response.json())
    .then(data => {
        var barColors = ["#727cf5"];

        // ✅ Tìm giá trị lớn nhất trong mảng dữ liệu
        const allData = data.series.flatMap(serie => serie.data);
        const maxY = Math.max(...allData);

        // ✅ Tính toán stepSize (ví dụ: chia làm 5 bước)
        const stepSize = Math.ceil(maxY / 5);

        var barOptions = {
            chart: {
                height: 256,
                type: "bar",
                stacked: false
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "20%"
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                show: true,
                width: 0,
                colors: ["transparent"]
            },
            series: data.series,
            zoom: { enabled: false },
            legend: { show: false },
            colors: $("#high-performing-product").data("colors") ? $("#high-performing-product").data("colors").split(",") : barColors,
            xaxis: {
                categories: data.categories,
                axisBorder: { show: false }
            },
            yaxis: {
                tickAmount: 5,
                labels: {
                    formatter: function (value) {
                        return value + "k";
                    },
                    offsetX: -15
                }
            },
            fill: { opacity: 1 },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return "$" + value + "k";
                    }
                }
            }
        };

        new ApexCharts(document.querySelector("#high-performing-product"), barOptions).render();
    })
    .catch(error => {
        console.error('Error fetching chart data:', error);
    });

    // Donut Chart (#average-sales)
    var donutColors = ["#727cf5", "#0acf97", "#fa5c7c", "#ffbc00"];
    var donutOptions = {
      chart: {
        height: 202,
        type: "donut"
      },
      legend: { show: false },
      stroke: { width: 0 },
      series: [44, 55, 41, 17],
      labels: ["Direct", "Affilliate", "Sponsored", "E-mail"],
      colors: $("#average-sales").data("colors") ? $("#average-sales").data("colors").split(",") : donutColors,
      responsive: [
        {
          breakpoint: 480,
          options: {
            chart: { width: 200 },
            legend: { position: "bottom" }
          }
        }
      ]
    };
    new ApexCharts(document.querySelector("#average-sales"), donutOptions).render();
  };

  Dashboard.prototype.initMaps = function () {
    new jsVectorMap({
      map: "world",
      selector: "#world-map-markers",
      zoomOnScroll: false,
      zoomButtons: true,
      markersSelectable: true,
      hoverOpacity: 0.7,
      hoverColor: false,
      regionStyle: {
        initial: {
          fill: "rgba(145, 166, 189, 0.25)"
        }
      },
      markerStyle: {
        initial: {
          r: 9,
          fill: "#727cf5",
          "fill-opacity": 0.9,
          stroke: "#fff",
          "stroke-width": 7,
          "stroke-opacity": 0.4
        },
        hover: {
          stroke: "#fff",
          "fill-opacity": 1,
          "stroke-width": 1.5
        }
      },
      backgroundColor: "transparent",
      markers: [
        { coords: [40.71, -74], name: "New York" },
        { coords: [37.77, -122.41], name: "San Francisco" },
        { coords: [-33.86, 151.2], name: "Sydney" },
        { coords: [1.3, 103.8], name: "Singapore" }
      ]
    });
  };

  Dashboard.prototype.init = function () {
    $("#dash-daterange").daterangepicker({ singleDatePicker: true });
    this.initCharts();
    this.initMaps();
  };

  $.Dashboard = new Dashboard();
  $.Dashboard.Constructor = Dashboard;
})(window.jQuery);

(function ($) {
  "use strict";
  $(document).ready(function () {
    $.Dashboard.init();
  });
})(window.jQuery);