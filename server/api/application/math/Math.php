<?php

class Math {
    public function getAnswers($a, $b, $c, $d = 0, $e = 0) {
        if ($a == 0 && $b == 0 && $c == 0 && $d == 0) {
            if ($e == 0) {
                return ['error' => 8001]; 
            } else {
                return ['error' => 8003]; 
            }
        }

        // определение степеней 
        if ($a != 0) {
            $roots = $this->solveQuartic($a, $b, $c, $d, $e);
        } elseif ($b != 0) {
            $roots = $this->solveCubic($b, $c, $d, $e);
        } elseif ($c != 0) {
            $roots = $this->solveQuadratic($c, $d, $e);
        } else {
            $roots = $this->solveLinear($d, $e);
        }

        // округление
        return array_map(function($root) {
            return round($root, 10);
        }, $roots);
    }

    private function solveLinear($a, $b) {
        if ($a == 0) {
            return [];
        }
        return [-$b / $a];
    }

    //уравнение 2 степени
    private function solveQuadratic($a, $b, $c) {
        $D = $b * $b - 4 * $a * $c;
        if ($D < 0) {
            return ['error' => 8002];
        } elseif ($D == 0) {
            return [-$b / (2 * $a)];
        } else {
            return [
                (-$b + sqrt($D)) / (2 * $a),
                (-$b - sqrt($D)) / (2 * $a)
            ];
        }
    }

    //уравнение 3 степени по Кордано
    private function solveCubic($a, $b, $c, $d) {
        // приведение уравнения к виду x^3 + px^2 + qx + r = 0
        $p = $b / $a;
        $q = $c / $a;
        $r = $d / $a;

        // преобразование к уравнению без члена второй степени
        $f = $q - $p * $p / 3;
        $g = $r - $p * $q / 3 + 2 * $p * $p * $p / 27;

        // Дискриминант
        $D = $g * $g / 4 + $f * $f * $f / 27;

        
        if ($D > 0) {
            $u = pow(-$g / 2 + sqrt($D), 1/3);
            $v = pow(-$g / 2 - sqrt($D), 1/3);
            $x1 = $u + $v - $p / 3;
            return [$x1];
        } elseif ($D == 0) {
            $u = pow(-$g / 2, 1/3);
            $x1 = 2 * $u - $p / 3;
            $x2 = -$u - $p / 3;
            return [$x1, $x2];
        } else {
            $phi = acos(-$g / 2 * sqrt(-27 / ($f * $f * $f)));
            $x1 = 2 * sqrt(-$f / 3) * cos($phi / 3) - $p / 3;
            $x2 = 2 * sqrt(-$f / 3) * cos(($phi + 2 * M_PI) / 3) - $p / 3;
            $x3 = 2 * sqrt(-$f / 3) * cos(($phi + 4 * M_PI) / 3) - $p / 3;
            return [$x1, $x2, $x3];
        }
    }

    //корни уравения 4 степени по Феррари
    private function solveQuartic($a, $b, $c, $d, $e) {
        // приведение уравнения к виду x^4 + px^3 + qx^2 + rx + s = 0
        $p = $b / $a;
        $q = $c / $a;
        $r = $d / $a;
        $s = $e / $a;

        $y_roots = $this->solveCubic(1, -$q, $p * $r - 4 * $s, -$p * $p * $s + 4 * $q * $s - $r * $r);
        $y = $y_roots[0]; 

        // вычисление коэф. для квадратных уравнений
        $R = sqrt($p * $p / 4 - $q + $y);
        
        if (abs($R) < 1e-10) {
            // если R близок к нулю, используем альтернативный подход
            $D1 = sqrt(3 * $p * $p / 4 - 2 * $q + 2 * sqrt($y * $y - 4 * $s));
            $D2 = sqrt(3 * $p * $p / 4 - 2 * $q - 2 * sqrt($y * $y - 4 * $s));
        } else {
            $D1 = sqrt(3 * $p * $p / 4 - $R * $R - 2 * $q + (4 * $p * $q - 8 * $r - $p * $p * $p) / (4 * $R));
            $D2 = sqrt(3 * $p * $p / 4 - $R * $R - 2 * $q - (4 * $p * $q - 8 * $r - $p * $p * $p) / (4 * $R));
        }

        $x1 = -$p / 4 + $R / 2 + $D1 / 2;
        $x2 = -$p / 4 + $R / 2 - $D1 / 2;
        $x3 = -$p / 4 - $R / 2 + $D2 / 2;
        $x4 = -$p / 4 - $R / 2 - $D2 / 2;

        return array_filter([$x1, $x2, $x3, $x4], function($root) {
            return is_numeric($root) && !is_nan($root);
        });
    }
}