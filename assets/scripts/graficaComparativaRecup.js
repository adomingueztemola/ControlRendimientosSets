$(function() {
    var a = c3.generate({
        bindto: "#rotated-axis",
        size: { height: 400 },
        color: { pattern: ["#4fc3f7", "#2962FF"] },
        data: {
            columns: [
                ["Stock", 50, 250, 90, 400, 300, 150],
                ["Real", 30, 100, 85, 50, 15, 25]
            ],
            types: { Stock: "bar" }
        },
        axis: { rotated: !0 },
        grid: { y: { show: !0 } }
    });
});