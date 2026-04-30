#include <stdio.h>

int main() {
    char message[10];

    printf("Enter a message: ");
    scanf("%9s", message);

    printf("%s", message);

    return 0;
}