<template>
    <canvas :id="canvasId" style="width: 100%; height: 100%;"></canvas>
</template>

<script>
    export default {
        props: ["attribute"],

        data() {
            return {
                canvasId: "attribute_chart_" + this._uid,
                chart: {},
                colors: ["red", "green", "blue", "orange", "yellow", "purple"]
            };
        },

        mounted() {
            this.chart = new Chart(document.getElementById(this.canvasId).getContext("2d"), {
                type: "scatter",
                options: {
                    scales: {
                        xAxes: [{
                            position: "bottom",
                            ticks: {
                                min: 0,
                                max: 100
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                max: 500
                            }
                        }]
                    }
                }
            });

            this.update();
        },

        watch: {
            attribute: {
                handler(value) {
                    this.update();
                },
                deep: true
            }
        },

        methods: {
            update: function () {
                this.chart.data.datasets = [{
                    label: this.attribute.name_i18n.en,
                    backgroundColor: this.colors[this.attribute.index],
                    data: this.evaluate()
                }];

                this.chart.update();
            },

            evaluate: function () {
                let data = [];

                for (let x = 0; x <= 100; x++) {
                    data.push({
                        x: x,
                        y: window.evaluateCurve(x, this.attribute.pivot.min, this.attribute.pivot.max, this.attribute.pivot.curve_type)
                    });
                }

                return data;
            }
        }
    };
</script>
