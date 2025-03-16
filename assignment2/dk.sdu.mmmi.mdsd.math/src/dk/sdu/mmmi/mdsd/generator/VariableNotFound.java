package dk.sdu.mmmi.mdsd.generator;

public class VariableNotFound extends Exception {

	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	
	private String variable;
	
	public VariableNotFound(String variable) {
		this.variable = variable;
	}
	
	public String getVariable() {
		return variable;
	}
}
