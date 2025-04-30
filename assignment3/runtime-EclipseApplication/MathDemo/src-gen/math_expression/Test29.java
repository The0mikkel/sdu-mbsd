package math_expression;
public class Test29 {
	public int x;
	public int y;
	
	private External external;
	
	public Test29(External external) {
	    this.external = external;
	}
	
	public void compute() {
		x = this.external.pow(4, 2);
		y = (x - 2);
	}
	public interface External {
		public int pow(int n0, int n1);
	}
}
