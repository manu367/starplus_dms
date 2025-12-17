
package com.highcharts.export.converter;

public class SVGConverterException extends Exception {

	private static final long serialVersionUID = -5110552374074051446L;
	private String mistake;

	public SVGConverterException() {
		super();
		mistake = "unknown to men";
	}

	public SVGConverterException(String err) {
		super(err); // call super class constructor
		mistake = err; // save message
	}

	public String getError() {
		return mistake;
	}

}
