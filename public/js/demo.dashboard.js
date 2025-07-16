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

        fetch('/line-chart-data', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                // Cập nhật tổng doanh thu tuần hiện tại và tuần trước
                const sum = (arr) => arr.reduce((a, b) => a + b, 0);
                const format = (value) => "$" + value.toLocaleString();

                const currentTotal = sum(data.current_week);
                const previousTotal = sum(data.previous_week);

                document.getElementById('current-week-total').textContent = format(currentTotal);
                document.getElementById('previous-week-total').textContent = format(previousTotal);

                // Biểu đồ doanh thu
                const revenueColors = document.querySelector("#revenue-chart").dataset.colors;
                const defaultColors = ["#727cf5", "#0acf97"];

                const options = {
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
                        { name: "Current Week", data: data.current_week },
                        { name: "Previous Week", data: data.previous_week }
                    ],
                    colors: revenueColors ? revenueColors.split(",") : defaultColors,
                    zoom: { enabled: false },
                    legend: { show: false },
                    xaxis: {
                        categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                        tooltip: { enabled: false },
                        axisBorder: { show: false }
                    },
                    grid: { strokeDashArray: 7 },
                    yaxis: {
                        labels: {
                            formatter: function (value) {
                                return "$" + value;
                            },
                            offsetX: -15
                        }
                    }
                };

                new ApexCharts(document.querySelector("#revenue-chart"), options).render();
            })
            .catch(err => {
                console.error("Lỗi lấy dữ liệu biểu đồ:", err);
            });

        fetch('/api/chart-data')
            .then(response => response.json())
            .then(data => {
                var barColors = ["#727cf5"];

                const allData = data.series.flatMap(serie => serie.data);
                const rawMaxY = Math.max(...allData);
                const rawMinY = Math.min(...allData);

                // ✅ Làm tròn về mốc gần nhất (bội của 10 hoặc 50 hoặc 100 tùy vào giá trị lớn nhất)
                const roundTo = rawMaxY > 1000 ? 100 : rawMaxY > 200 ? 50 : 10;

                const maxY = Math.ceil(rawMaxY / roundTo) * roundTo;
                const minY = Math.floor(rawMinY / roundTo) * roundTo;

                const tickCount = 5;
                const rangeY = maxY - minY;
                const stepSize = Math.ceil(rangeY / tickCount);

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
                        min: minY,
                        max: maxY,
                        tickAmount: tickCount,
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


        fetch('/device-chi-win-rate', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
            .then(res => res.json())
            .then(data => {
                const generateColors = (n) => {
                    const colors = [];
                    for (let i = 0; i < n; i++) {
                        const hue = (i * 360 / n) % 360;
                        colors.push(`hsl(${hue}, 70%, 60%)`);
                    }
                    return colors;
                };

                const colors = generateColors(data.labels.length);

                // Render Donut Chart
                const donutOptions = {
                    chart: { height: 202, type: "donut" },
                    labels: data.labels,
                    series: data.series,
                    colors: colors,
                    legend: { show: true },
                    stroke: { width: 0 },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: { width: 200 },
                            legend: { position: "bottom" }
                        }
                    }]
                };

                new ApexCharts(document.querySelector("#average-sales"), donutOptions).render();

                // Cập nhật chú thích dưới biểu đồ
                const legendList = document.getElementById("chart-widget-list");
                legendList.innerHTML = ""; // Xóa cũ

                data.labels.forEach((label, i) => {
                    const color = colors[i];
                    const value = data.series[i].toLocaleString();

                    const p = document.createElement("p");
                    p.innerHTML = `<i class="mdi mdi-square" style="color: ${color}"></i> ${label}
                       <span class="float-end">${value}%</span>`;
                    legendList.appendChild(p);
                });
            })
            .catch(err => console.error("Lỗi biểu đồ chi chiến thắng:", err));


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