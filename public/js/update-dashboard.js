(function ($) {
  "use strict";

  function DashboardUpdater() {
    this.$body = $("body");
  }

  DashboardUpdater.prototype.updateStats = function () {
    // Gọi API để lấy dữ liệu
    fetch('/api/dashboard-stats')
      .then(response => response.json())
      .then(data => {
        // Cập nhật Tổng số chi ăn 1 tuần
        const weeklySpendingElement = this.$body.find('.card.widget-flat h5[title="Number of Customers"]').siblings('h3');
        const weeklySpendingChangeElement = this.$body.find('.card.widget-flat h5[title="Number of Customers"]').siblings('p').find('.text-success, .text-danger');
        weeklySpendingElement.text(`+ ${data.weeklySpending} Chi`);
        weeklySpendingChangeElement
          .removeClass('text-success text-danger')
          .addClass(data.weeklySpendingChange >= 0 ? 'text-success' : 'text-danger')
          .html(`<i class="mdi ${data.weeklySpendingChange >= 0 ? 'mdi-arrow-up-bold' : 'mdi-arrow-down-bold'}"></i> ${Math.abs(data.weeklySpendingChange).toFixed(2)}%`);

        // Cập nhật Số thiết bị online
        const onlineDevicesElement = this.$body.find('.card.widget-flat h5[title="Number of Orders"]').siblings('h3');
        const onlineDevicesChangeElement = this.$body.find('.card.widget-flat h5[title="Number of Orders"]').siblings('p').find('.text-success, .text-danger');
        onlineDevicesElement.text(data.onlineDevices.toLocaleString());
        onlineDevicesChangeElement
          .removeClass('text-success text-danger')
          .addClass(data.onlineDevicesChange >= 0 ? 'text-success' : 'text-danger')
          .html(`<i class="mdi ${data.onlineDevicesChange >= 0 ? 'mdi-arrow-up-bold' : 'mdi-arrow-down-bold'}"></i> ${Math.abs(data.onlineDevicesChange).toFixed(2)}%`);

        // Cập nhật Tổng số chi ăn 1 ngày
        const dailySpendingElement = this.$body.find('.card.widget-flat h5[title="Average Revenue"]').siblings('h3');
        const dailySpendingChangeElement = this.$body.find('.card.widget-flat h5[title="Average Revenue"]').siblings('p').find('.text-success, .text-danger');
        dailySpendingElement.text(`+ ${data.dailySpending} Chi`);
        dailySpendingChangeElement
          .removeClass('text-success text-danger')
          .addClass(data.dailySpendingChange >= 0 ? 'text-success' : 'text-danger')
          .html(`<i class="mdi ${data.dailySpendingChange >= 0 ? 'mdi-arrow-up-bold' : 'mdi-arrow-down-bold'}"></i> ${Math.abs(data.dailySpendingChange).toFixed(2)}%`);

        // Cập nhật Lợi nhuận tuần
        const weeklyProfitElement = this.$body.find('.card.widget-flat h5[title="Growth"]').siblings('h3');
        const weeklyProfitChangeElement = this.$body.find('.card.widget-flat h5[title="Growth"]').siblings('p').find('.text-success, .text-danger');
        weeklyProfitElement.text(`+ ${data.weeklyProfit.toLocaleString()}K`);
        weeklyProfitChangeElement
          .removeClass('text-success text-danger')
          .addClass(data.weeklyProfitChange >= 0 ? 'text-success' : 'text-danger')
          .html(`<i class="mdi ${data.weeklyProfitChange >= 0 ? 'mdi-arrow-up-bold' : 'mdi-arrow-down-bold'}"></i> ${Math.abs(data.weeklyProfitChange).toFixed(2)}%`);
      })
      .catch(error => {
        console.error('Error fetching dashboard stats:', error);
      });
  };

  DashboardUpdater.prototype.init = function () {
    this.updateStats();
  };

  $.DashboardUpdater = new DashboardUpdater();
  $.DashboardUpdater.Constructor = DashboardUpdater;

})(window.jQuery);

(function ($) {
  "use strict";
  $(document).ready(function () {
    $.DashboardUpdater.init();
  });
})(window.jQuery);