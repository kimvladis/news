$(function () {
  $('#notification-event').on('change', function () {
    $.get('/notification/params', {event: $(this).val()}, function (data) {
      $('#params').html(data.reduce(function (prev, cur) {
        return prev + '{' + cur + '} '
      }, ''));
    });
  }).trigger('change');
  $('.js-notified').on('click', function () {
    $(this).prop('disabled', true);
    $.post('/notification/notified', {id: $(this).data('id')}, $.proxy(function () {
      $(this).hide().closest('.alert').removeClass('alert-info').addClass('alert-warning');
    }, this));
  });
});

