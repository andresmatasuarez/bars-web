export default function collapsible(triggerDataAttr, targetDataAttr) {
  const triggerSelector = `[data-${triggerDataAttr}]`;
  $(triggerSelector).map(function () {
    const triggerElement = $(this);

    const collapsibleId = triggerElement.data(triggerDataAttr);

    triggerElement.click(function () {
      const targetSelector = `[data-${targetDataAttr}="${collapsibleId}"]`;
      $(targetSelector).map(function () {
        const targetElement = $(this);
        const isCollapsed = targetElement.attr('data-collapsed');
        if (isCollapsed !== undefined) {
          triggerElement.removeAttr('data-collapsed');
          targetElement.removeAttr('data-collapsed');
        } else {
          triggerElement.attr('data-collapsed', '');
          targetElement.attr('data-collapsed', '');
        }
      });
    });
  });
}
