<?php

namespace Dealer4Dealer\SubstituteOrders\Block\Order\Search;

use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Framework\Api\ArrayObjectSearch;

use function Dealer4Dealer\SubstituteOrders\Block\Order\Search\__;

class DatePickerField extends \Magento\Customer\Block\Widget\AbstractWidget
{
    /**
     * Constants for borders of date-type customer attributes
     */
    const MIN_DATE_RANGE_KEY = 'date_range_min';

    const MAX_DATE_RANGE_KEY = 'date_range_max';

    /**
     * Date inputs
     *
     * @var array
     */
    protected $_dateInputs = [];

    /**
     * @var \Magento\Framework\View\Element\Html\Date
     */
    protected $dateElement;

    /**
     * @var \Magento\Framework\Data\Form\FilterFactory
     */
    protected $filterFactory;

    protected $label;

    protected $htmlId;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param CustomerMetadataInterface $customerMetadata
     * @param \Magento\Framework\View\Element\Html\Date $dateElement
     * @param \Magento\Framework\Data\Form\FilterFactory $filterFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        CustomerMetadataInterface $customerMetadata,
        \Magento\Framework\View\Element\Html\Date $dateElement,
        \Magento\Framework\Data\Form\FilterFactory $filterFactory,
        array $data = []
    ) {
        $this->dateElement = $dateElement;
        $this->filterFactory = $filterFactory;
        parent::__construct($context, $addressHelper, $customerMetadata, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('order/search/datepicker.phtml');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        $attributeMetadata = $this->_getAttribute('dob');
        return $attributeMetadata ? (bool)$attributeMetadata->isVisible() : false;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        $attributeMetadata = $this->_getAttribute('dob');
        return $attributeMetadata ? (bool)$attributeMetadata->isRequired() : false;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->setTime($date ? strtotime($date) : false);
        $this->setValue($this->applyOutputFilter($date));
        return $this;
    }

    /**
     * Return Data Form Filter or false
     *
     * @return \Magento\Framework\Data\Form\Filter\FilterInterface|false
     */
    protected function getFormFilter()
    {
        $attributeMetadata = $this->_getAttribute('dob');
        $filterCode = $attributeMetadata->getInputFilter();
        if ($filterCode) {
            $data = [];
            if ($filterCode == 'date') {
                $data['format'] = $this->getDateFormat();
            }

            $filter = $this->filterFactory->create($filterCode, $data);
            return $filter;
        }

        return false;
    }

    /**
     * Apply output filter to value
     *
     * @param string $value
     * @return string
     */
    protected function applyOutputFilter($value)
    {
        $filter = $this->getFormFilter();
        if ($filter) {
            $value = $filter->outputFilter($value);
        }

        return $value;
    }

    /**
     * @return string|bool
     */
    public function getDay()
    {
        return $this->getTime() ? date('d', $this->getTime()) : '';
    }

    /**
     * @return string|bool
     */
    public function getMonth()
    {
        return $this->getTime() ? date('m', $this->getTime()) : '';
    }

    /**
     * @return string|bool
     */
    public function getYear()
    {
        return $this->getTime() ? date('Y', $this->getTime()) : '';
    }

    /**
     * Return label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return ($this->label) ? $this->label : __('Date');
    }

    /**
     * Create correct date field
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $this->dateElement->setData(
            [
            'extra_params' => $this->getHtmlExtraParams(),
            'name' => $this->getHtmlId(),
            'id' => $this->getHtmlId(),
            'class' => $this->getHtmlClass(),
            'value' => $this->getRequest()->getParam($this->getHtmlId()),
            'date_format' => $this->getDateFormat(),
            'image' => $this->getViewFileUrl('Magento_Theme::calendar.png'),
            'years_range' => '-120y:c+nn',
            'max_date' => '-0d',
            'change_month' => 'true',
            'change_year' => 'true',
            'show_on' => 'both'
            ]
        );
        return $this->dateElement->getHtml();
    }

    /**
     * Return id
     *
     * @return string
     */
    public function getHtmlId()
    {
        return $this->htmlId;
    }

    /**
     * Return data-validate rules
     *
     * @return string
     */
    public function getHtmlExtraParams()
    {
        $validators = [];

        if ($this->isRequired()) {
            $validators['required'] = true;
        }

        $validators['validate-date'] = [
            'dateFormat' => $this->getDateFormat()
        ];

        return 'data-validate="' . $this->_escaper->escapeHtml(json_encode($validators)) . '"';
    }

    /**
     * Escape a string for the HTML attribute context
     *
     * @param string $string
     * @param boolean $escapeSingleQuote
     * @return string
     * @since 100.2.0
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
        return $this->_escaper->escapeHtmlAttr($string, $escapeSingleQuote);
    }

    /**
     * Returns format which will be applied for DOB in javascript
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
    }

    /**
     * Add date input html
     *
     * @param string $code
     * @param string $html
     * @return void
     */
    public function setDateInput($code, $html)
    {
        $this->_dateInputs[$code] = $html;
    }

    /**
     * Sort date inputs by dateformat order of current locale
     *
     * @param bool $stripNonInputChars
     *
     * @return string
     */
    public function getSortedDateInputs($stripNonInputChars = true)
    {
        $mapping = [];
        if ($stripNonInputChars) {
            $mapping['/[^medy]/i'] = '\\1';
        }

        $mapping['/m{1,5}/i'] = '%1$s';
        $mapping['/e{1,5}/i'] = '%2$s';
        $mapping['/d{1,5}/i'] = '%2$s';
        $mapping['/y{1,5}/i'] = '%3$s';

        $dateFormat = preg_replace(array_keys($mapping), array_values($mapping), $this->getDateFormat());

        return sprintf($dateFormat, $this->_dateInputs['m'], $this->_dateInputs['d'], $this->_dateInputs['y']);
    }

    /**
     * Return minimal date range value
     *
     * @return string|null
     */
    public function getMinDateRange()
    {
        $dob = $this->_getAttribute('dob');
        if ($dob !== null) {
            $rules = $this->_getAttribute('dob')->getValidationRules();
            $minDateValue = ArrayObjectSearch::getArrayElementByName(
                $rules,
                self::MIN_DATE_RANGE_KEY
            );
            if ($minDateValue !== null) {
                return date("Y/m/d", $minDateValue);
            }
        }

        return null;
    }

    /**
     * Return maximal date range value
     *
     * @return string|null
     */
    public function getMaxDateRange()
    {
        $dob = $this->_getAttribute('dob');
        if ($dob !== null) {
            $rules = $this->_getAttribute('dob')->getValidationRules();
            $maxDateValue = ArrayObjectSearch::getArrayElementByName(
                $rules,
                self::MAX_DATE_RANGE_KEY
            );
            if ($maxDateValue !== null) {
                return date("Y/m/d", $maxDateValue);
            }
        }

        return null;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param mixed $htmlId
     * @return $this
     */
    public function setHtmlId($htmlId)
    {
        $this->htmlId = $htmlId;
        return $this;
    }
}
