
package com.highcharts.export.pool;

public class PoolException extends Exception {

	private static final long serialVersionUID = 3925816328286206059L;
	private final String mistake;

	public PoolException() {
		super();
		mistake = "unknown to men";
	}

	public PoolException(String err) {
		super(err); // call super class constructor
		mistake = err; // save message
	}

	public String getError() {
		return mistake;
	}

}
