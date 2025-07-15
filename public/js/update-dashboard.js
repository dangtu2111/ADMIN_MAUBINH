(function ($) {
  "use strict";

  function DashboardUpdater() {
    this.$body = $("body");
  }

  DashboardUpdater.prototype.updateStats = function () {
    fetch('/api/dashboard-stats')
      .then(response => response.json())
      .then(data => {
        // Tổng số chi ăn 1 tuần
        const weeklySpendingElement = this.$body.find('.card.widget-flat h5[title="Number of Customers"]').siblings('h3');
        const weeklySpendingChangeElement = this.$body.find('.card.widget-flat h5[title="Number of Customers"]').siblings('p').find('.text-success, .text-danger');
        const weeklySpendingFormatted = (data.weeklySpending > 0 ? `+ ${data.weeklySpending}` : data.weeklySpending) + " Chi";
        weeklySpendingElement.text(weeklySpendingFormatted);
        weeklySpendingChangeElement
          .removeClass('text-success text-danger')
          .addClass(data.weeklySpendingChange >= 0 ? 'text-success' : 'text-danger')
          .html(`<i class="mdi ${data.weeklySpendingChange >= 0 ? 'mdi-arrow-up-bold' : 'mdi-arrow-down-bold'}"></i> ${Math.abs(data.weeklySpendingChange).toFixed(2)}%`);

        // Thiết bị online
        const onlineDevicesElement = this.$body.find('.card.widget-flat h5[title="Number of Orders"]').siblings('h3');
        const onlineDevicesChangeElement = this.$body.find('.card.widget-flat h5[title="Number of Orders"]').siblings('p').find('.text-success, .text-danger');
        onlineDevicesElement.text(data.onlineDevices.toLocaleString());
        onlineDevicesChangeElement
          .removeClass('text-success text-danger')
          .addClass(data.onlineDevicesChange >= 0 ? 'text-success' : 'text-danger')
          .html(`<i class="mdi ${data.onlineDevicesChange >= 0 ? 'mdi-arrow-up-bold' : 'mdi-arrow-down-bold'}"></i> ${Math.abs(data.onlineDevicesChange).toFixed(2)}%`);

        // Tổng số chi ăn 1 ngày
        const dailySpendingElement = this.$body.find('.card.widget-flat h5[title="Average Revenue"]').siblings('h3');
        const dailySpendingChangeElement = this.$body.find('.card.widget-flat h5[title="Average Revenue"]').siblings('p').find('.text-success, .text-danger');
        const dailySpendingFormatted = (data.dailySpending > 0 ? `+ ${data.dailySpending}` : data.dailySpending) + " Chi";
        dailySpendingElement.text(dailySpendingFormatted);
        dailySpendingChangeElement
          .removeClass('text-success text-danger')
          .addClass(data.dailySpendingChange >= 0 ? 'text-success' : 'text-danger')
          .html(`<i class="mdi ${data.dailySpendingChange >= 0 ? 'mdi-arrow-up-bold' : 'mdi-arrow-down-bold'}"></i> ${Math.abs(data.dailySpendingChange).toFixed(2)}%`);

        // Lợi nhuận tuần
        const weeklyProfitElement = this.$body.find('.card.widget-flat h5[title="Growth"]').siblings('h3');
        const weeklyProfitChangeElement = this.$body.find('.card.widget-flat h5[title="Growth"]').siblings('p').find('.text-success, .text-danger');
        const weeklyProfitFormatted = (data.weeklyProfit > 0 ? `+ ${data.weeklyProfit.toLocaleString()}` : data.weeklyProfit.toLocaleString()) + "K";
        weeklyProfitElement.text(weeklyProfitFormatted);
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
