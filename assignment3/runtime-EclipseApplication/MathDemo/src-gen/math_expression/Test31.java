package math_expression;
public class Test31 {
	public int x;
	public int y;
	
	private External external;
	
	public Test31(External external) {
	    this.external = external;
	}
	
	public void compute() {
		x = 5;
		y = (2 + this.external.pow((x - 3), (3 * 2)));
	}
	public interface External {
		public int pow(int n0, int n1);
	}
}
