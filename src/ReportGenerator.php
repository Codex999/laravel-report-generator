<?php

namespace Jimmyjs\ReportGenerator;

use Config;
use Illuminate\Support\Str;

class ReportGenerator
{
	protected $applyFlush;
	protected $headers;
	protected $columns;
	protected $query;
	protected $limit = null;
	protected $groupByArr = [];
	protected $paper = 'a4';
	protected $orientation = 'portrait';
	protected $editColumns = [];
	protected $showNumColumn = true;
	protected $showTotalColumns = [];
	protected $styles = [];
	protected $simpleVersion = false;
	protected $withoutManipulation = false;
    protected $showMeta = true;
    protected $showHeader = true;
    protected $customTemplate = null;
    protected $customTemplateVariables = null;
    protected $customHeaderTemplate = null;
    protected $customFooterTemplate = null;
    protected $margins = [15,15,10,10];
    protected $customPageNumber = "";
    protected $query_results_data;

	public function __construct()
	{
		$this->applyFlush = (bool) Config::get('report-generator.flush', true);
	}

	public function of($title, Array $meta, $query, Array $columns)
	{
		$this->headers = [
			'title' => $title,
			'meta'  => $meta
		];

		$this->query = $query;
		$this->columns = $this->mapColumns($columns);

		return $this;
	}

    public function showHeader($value = true)
    {
        $this->showHeader = $value;

        return $this;
    }

    public function showMeta($value = true)
    {
        $this->showMeta = $value;

        return $this;
    }

    public function showNumColumn($value = true)
    {
        $this->showNumColumn = $value;

        return $this;
    }

	public function simple()
	{
		$this->simpleVersion = true;

		return $this;
	}

	public function withoutManipulation()
	{
		$this->withoutManipulation = true;

		return $this;
	}

	private function mapColumns(Array $columns)
	{
		$result = [];

		foreach ($columns as $name => $data) {
			if (is_int($name)) {
				$result[$data] = Str::snake($data);
			} else {
				$result[$name] = $data;
			}
		}

		return $result;
	}

	public function setPaper($paper)
	{
		$this->paper = strtolower($paper);

		return $this;
	}

	public function editColumn($columnName, Array $options)
	{
		foreach ($options as $option => $value) {
			$this->editColumns[$columnName][$option] = $value;
		}

		return $this;
	}

	public function editColumns(Array $columnNames, Array $options)
	{
		foreach ($columnNames as $columnName) {
			$this->editColumn($columnName, $options);
		}

		return $this;
	}

	public function showTotal(Array $columns)
	{
		$this->showTotalColumns = $columns;

		return $this;
	}

	public function groupBy($column)
	{
		if (is_array($column)) {
			$this->groupByArr = $column;
		} else {
			array_push($this->groupByArr, $column);
		}

		return $this;
	}

	public function limit($limit)
	{
		$this->limit = $limit;

		return $this;
	}

	public function setOrientation($orientation)
	{
		$this->orientation = strtolower($orientation);

		return $this;
	}

	public function setCss(Array $styles)
	{
		foreach ($styles as $selector => $style) {
			array_push($this->styles, [
				'selector' => $selector,
				'style' => $style
			]);
		}

		return $this;
	}

	public function fromTemplate(string $customTemplate, array $customTemplateVariables)
	{
		$this->customTemplate = $customTemplate;
		$this->customTemplateVariables = $customTemplateVariables;

		return $this;
	}

	public function customHeader(string $customHeaderTemplate)
	{
		$this->customHeaderTemplate = $customHeaderTemplate;

		return $this;
	}

	public function customFooter(string $customFooterTemplate)
	{
		$this->customFooterTemplate = $customFooterTemplate;

		return $this;
	}

	public function margins(int $marginTop = 15, int $marginBottom = 15, int $marginLeft = 10, int $marginRight = 10)
	{
		$this->margins = [$marginTop, $marginBottom, $marginLeft, $marginRight];

		return $this;
	}

	public function customPageNumber(string $customPageNumber)
	{
		$this->customPageNumber = $customPageNumber;
		return $this;
	}
	
	
	public function csv_data($query_results_data)
	{
		$this->query_results_data = $query_results_data;
		return $this;
	}
}
