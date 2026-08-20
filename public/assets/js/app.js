/* منصة نقاطي — سكربتات واجهة بسيطة بدون مكتبات خارجية. */

(function () {
  'use strict';

  // إخفاء رسائل النظام تلقائياً
  setTimeout(function () {
    document.querySelectorAll('.flash').forEach(function (el) {
      el.style.transition = 'opacity .4s ease';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    });
  }, 6000);

  // النوافذ المنبثقة: data-open="#id" / data-close
  document.addEventListener('click', function (event) {
    var opener = event.target.closest('[data-open]');
    if (opener) {
      var dialog = document.querySelector(opener.getAttribute('data-open'));
      if (dialog && typeof dialog.showModal === 'function') {
        event.preventDefault();
        dialog.showModal();
      }
      return;
    }

    var closer = event.target.closest('[data-close]');
    if (closer) {
      var parent = closer.closest('dialog');
      if (parent) {
        event.preventDefault();
        parent.close();
      }
    }
  });

  // تأكيد الحذف
  document.addEventListener('submit', function (event) {
    var form = event.target;
    var message = form.getAttribute('data-confirm');
    if (message && !window.confirm(message)) {
      event.preventDefault();
    }
  });

  // تحديد كل الطلاب في شاشة الرصد
  var selectAll = document.querySelector('[data-select-all]');
  if (selectAll) {
    selectAll.addEventListener('click', function () {
      var boxes = document.querySelectorAll('.student-pick input[type="checkbox"]');
      var target = selectAll.dataset.state !== 'on';
      boxes.forEach(function (box) { box.checked = target; });
      selectAll.dataset.state = target ? 'on' : 'off';
      selectAll.textContent = target ? 'إلغاء التحديد' : 'تحديد الكل';
      updateSelectedCount();
    });
  }

  function updateSelectedCount() {
    var counter = document.querySelector('[data-selected-count]');
    if (!counter) return;
    counter.textContent = document.querySelectorAll('.student-pick input:checked').length;
  }

  document.addEventListener('change', function (event) {
    if (event.target.matches('.student-pick input')) {
      updateSelectedCount();
    }
    // تحديد سريع لحالة الحضور للجميع
    if (event.target.matches('[data-mark-all]')) {
      var status = event.target.value;
      if (!status) return;
      document.querySelectorAll('input[type="radio"][value="' + status + '"]').forEach(function (radio) {
        if (radio.name.indexOf('status[') === 0) radio.checked = true;
      });
      event.target.selectedIndex = 0;
    }
  });

  updateSelectedCount();

  // بحث فوري داخل الجداول: data-filter-target="#table"
  var filterInput = document.querySelector('[data-filter-target]');
  if (filterInput) {
    filterInput.addEventListener('input', function () {
      var table = document.querySelector(filterInput.getAttribute('data-filter-target'));
      if (!table) return;
      var term = filterInput.value.trim();
      table.querySelectorAll('tbody tr').forEach(function (row) {
        row.style.display = term === '' || row.textContent.indexOf(term) !== -1 ? '' : 'none';
      });
    });
  }

  // نسخ الروابط
  document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-copy]');
    if (!button) return;
    event.preventDefault();
    var text = button.getAttribute('data-copy');
    var done = function () {
      var original = button.textContent;
      button.textContent = 'تم النسخ ✓';
      setTimeout(function () { button.textContent = original; }, 1600);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(done, function () { window.prompt('انسخ الرابط:', text); });
    } else {
      window.prompt('انسخ الرابط:', text);
    }
  });

  // إضافة سؤال جديد في منشئ المسابقات
  var addQuestion = document.querySelector('[data-add-question]');
  if (addQuestion) {
    addQuestion.addEventListener('click', function () {
      var list = document.querySelector('#questions-list');
      var index = list.children.length;
      var template = document.querySelector('#question-template').innerHTML;
      var wrapper = document.createElement('div');
      wrapper.className = 'quiz-question';
      wrapper.innerHTML = template.replace(/__INDEX__/g, String(index)).replace(/__NUM__/g, String(index + 1));
      list.appendChild(wrapper);
    });
  }
})();
