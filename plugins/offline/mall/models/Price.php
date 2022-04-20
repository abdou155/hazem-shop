<?php namespace OFFLINE\Mall\Models;

use Model;
use October\Rain\Database\Traits\Nullable;
use OFFLINE\Mall\Classes\Utils\Money;

class Price extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use Nullable;

    public $nullable = ['price'];
    public $rules = [
    ];
    public $table = 'offline_mall_prices';
    public $morphTo = [
        'priceable' => [],
    ];
    public $fillable = [
        'currency_id',
        'price_category_id',
        'priceable_id',
        'priceable_type',
        'price',
        'field',
    ];
    public $belongsTo = [
        'category' => [PriceCategory::class, 'key' => 'price_category_id'],
        'currency' => [Currency::class],
    ];
    /**
     * @var Money
     */
    protected $money;
    /**
     * Flag that indicates that this Price was automatically
     * calculated from the base currency.
     * @var bool
     */
    public $autoGenerated = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->money = app(Money::class);
    }

    public function beforeCreate()
    {
        if ($this->price === null) {
            return false;
        }
    }

    public function beforeSave()
    {
        if ($this->price === null) {
            return $this->delete();
        }
    }

    public function setPriceAttribute($value)
    {
        if ($value === null || $value === '') {
            return $this->attributes['price'] = null;
        }

        if ($value === 0 || $value === '0' || $value === '0.00') {
            return $this->attributes['price'] = 0;
        }

        $this->attributes['price'] = round(((float)$value) * 100, 0);
    }

    public function getFloatAttribute()
    {
        if ($this->price === null) {
            return null;
        }

        return (float)($this->price / 100);
    }

    public function getDecimalAttribute()
    {
        if ($this->price === null) {
            return null;
        }

        return number_format($this->price / 100, 2, '.', '');
    }

    public function getIntegerAttribute()
    {
        if ($this->price === null) {
            return null;
        }

        return (int)$this->price;
    }

    public function getStringAttribute()
    {
        if ($this->price === null) {
            return null;
        }

        return (string)$this;
    }

    /**
     * Return a new instance of this model with a modified price value.
     */
    public function withPrice($price): Price
    {
        $new        = $this->replicate();
        $new->price = $price;

        return $new;
    }

    /**
     * Return a new instance of this model with a reduced price value.
     */
    public function withDiscountPercentage($percentage): Price
    {
        return $this->withPrice($this->price * (100 - $percentage) / 10000);
    }

    /**
     * Returns a new price model from a price array.
     *
     * @param array $input
     *
     * @return Price
     */
    public static function fromArray(array $input): Price
    {
        $value = array_get($input, Currency::activeCurrency()->code);

        return new self(['price' => $value / 100]);
    }

    public function __toString()
    {
        $model = $this instanceof Product || $this instanceof Variant ? $this : null;

        return $this->money->format($this->integer, $model, $this->currency);
    }

    public function toArray()
    {
        return [
            'id'              => $this->id,
            'price'           => $this->price,
            'price_formatted' => (string)$this,
            'currency'        => [
                'id'       => $this->currency->id,
                'code'     => $this->currency->code,
                'symbol'   => $this->currency->symbol,
                'rate'     => $this->currency->rate,
                'decimals' => $this->currency->decimals,
            ],
        ];
    }
}
